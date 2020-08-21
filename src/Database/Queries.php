<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\StaticClass;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Rank\RankingQueries;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

final class Queries
{
    use StaticClass;

    public static function createRankedListTable(Connection $database): void
    {
        $database->exec(
            'CREATE TABLE IF NOT EXISTS rank (
                list_id TEXT NOT NULL,
                rank INTEGER NOT NULL,
                app_id INTEGER NOT NULL,
                score REAL,
                owner TEXT,
                PRIMARY KEY(list_id, rank)
            )'
        );
    }

    public static function recreateRankedListTable(Connection $database): void
    {
        $database->exec('DROP TABLE IF EXISTS rank');
        self::createRankedListTable($database);
    }

    /**
     * Fetches a previously saved ranking. If a previous database is specified, ranking movements are calculated
     * relative to the previous ranking position.
     *
     * @param Connection $database
     * @param Ranking $ranking Ranking.
     * @param string $prevDb Optional. Path to a previous database.
     *
     * @return array Ranking of Steam apps.
     */
    public static function fetchRankedList(Connection $database, Ranking $ranking, string $prevDb = null): array
    {
        $query = $database->createQueryBuilder()
            ->select('rank.*, app.*, t250.rank as rank_250')
            ->from('rank')
            ->join('rank', 'app', 'app', 'id = rank.app_id')
            ->leftJoin('rank', 'rank', 't250', 't250.list_id = "top250" AND rank.app_id = t250.app_id')
            ->where('rank.list_id = :list_id')
                ->setParameter('list_id', $ranking->getId())
            ->orderBy('rank')
            ->setMaxResults($ranking->getLimit())
        ;

        if ($prevDb) {
            $database->exec("ATTACH '$prevDb' AS prev");

            $query
                ->addSelect('prank.rank - rank.rank AS movement')
                ->leftJoin(
                    'rank',
                    'prev.rank',
                    'prank',
                    'rank.list_id = prank.list_id AND
                        (rank.owner = prank.owner OR (rank.owner IS NULL AND rank.app_id = prank.app_id))'
                )
            ;
        }

        if ($ranking instanceof CustomRankingFetch) {
            $ranking->customizeRankingFetch($query);
        }

        $list = $query->execute()->fetchAll();

        $prevDb && $database->exec('DETACH prev');

        return $list;
    }

    /**
     * Fetches a list of apps present in the previous database snapshot but "missing" from the current snapshot.
     */
    public static function fetchMissingApps(Connection $database, Ranking $ranking, int $limit, string $prevDb): array
    {
        $database->exec("ATTACH '$prevDb' AS prev");

        try {
            return $database->executeQuery(
                'SELECT id, prank.rank, prank.owner, name, videos, "dead" movement
                FROM prev.rank prank
                    JOIN app ON id = prank.app_id
	                LEFT JOIN rank USING (list_id, app_id)
                WHERE prank.list_id = :ranking_id AND rank.app_id IS NULL
                ORDER BY prank.rank
                LIMIT :limit',
                [
                    'ranking_id' => $ranking->getId(),
                    'limit' => $limit,
                ]
            )->fetchAll();
        } finally {
            $database->exec('DETACH prev');
        }
    }

    public static function fetchAppTags(Connection $database, int $appId): array
    {
        return $database->query("SELECT tag FROM app_tag WHERE app_id = $appId ORDER BY votes DESC")
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function fetchPopularTags(Connection $database, int $threshold = 400): array
    {
        return $database->query(
            "SELECT tag, COUNT(tag) AS count
            FROM app_tag
            LEFT JOIN app ON id = app_id
            WHERE type = 'game' AND tag != 'VR'
            GROUP BY tag
            HAVING count >= $threshold"
        )->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function fetchPatronReviews(Connection $database, int $appId): array
    {
        return $database->fetchAll(
            "SELECT * FROM patron_review
            INNER JOIN steam_profile ON patron_review.profile_id = steam_profile.profile_id
            WHERE app_id = $appId
            ORDER BY positive DESC"
        );
    }

    /**
     * Ranks the specified list according to the list's algorithm and weighting.
     *
     * @param Connection $database
     * @param Ranking $ranking List.
     *
     * @return Statement
     */
    public static function rankList(Connection $database, Ranking $ranking): Statement
    {
        $query = self::createScorer($database, $ranking)
            ->orderBy('score', SortDirection::DESC)
            ->setMaxResults($ranking->getLimit())
        ;

        $ranking->customizeQuery($query);

        return $query->execute();
    }

    public static function createScorer(Connection $database, Ranking $ranking): QueryBuilder
    {
        $query = $database->createQueryBuilder()
            ->addSelect('*')
            ->from('app')
            // Ensure platforms are defined to exclude discontinued apps.
            ->where('type = \'game\' AND platforms > 0')
        ;

        self::removeBannedGames($query);
        self::calculateScore($query, $ranking);

        return $query;
    }

    /**
     * Removes banned games the specified query.
     *
     * For now, in the interests of time, banned games are hard coded. However, a heuristic is preferred to treat all
     * games fairly and catch any new offenders. The proposed heuristic selects non-free games that are still available
     * to purchase but have less than 1% of their total reviews from verified Steam purchasers, where total reviews
     * exceed 1,000.
     *
     * This heuristic has already been run in the selection of currently banned games and only found one real offender
     * that is systematically abusing bot accounts to inflate review scores.
     *
     * @param QueryBuilder $query Query.
     */
    private static function removeBannedGames(QueryBuilder $query): void
    {
        $query->andWhere('id != 252150');
    }

    public static function calculateScore(
        QueryBuilder $builder,
        Ranking $ranking,
        string $prefix = 'app',
        string $alias = 'score'
    ): void {
        switch ($ranking->getAlgorithm()) {
            case Algorithm::WILSON:
                RankingQueries::calculateWilsonScore($builder, $ranking->getWeight());
                break;

            case Algorithm::BAYESIAN:
                RankingQueries::calculateBayesianScore($builder, $ranking->getWeight());
                break;

            case Algorithm::LAPLACE:
                RankingQueries::calculateLaplaceScore($builder, $ranking->getWeight());
                break;

            case Algorithm::LAPLACE_LOG:
                RankingQueries::calculateLaplaceLogScore($builder, $ranking->getWeight(), $prefix, $alias);
                break;

            case Algorithm::DIRICHLET_PRIOR:
                RankingQueries::calculateDirichletPriorScore($builder, $ranking->getWeight());
                break;

            case Algorithm::DIRICHLET_PRIOR_LOG:
                RankingQueries::calculateDirichletPriorLogScore($builder, $ranking->getWeight());
                break;

            case Algorithm::TORN:
                RankingQueries::calculateTornScore($builder, $ranking->getWeight());
                break;

            case Algorithm::HIDDEN_GEMS:
                RankingQueries::calculateHiddenGemsScore($builder, $ranking->getWeight());
                break;
        }
    }
}
