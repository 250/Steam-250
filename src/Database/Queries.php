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
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Club250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

final class Queries
{
    use StaticClass;

    public static function createRankedListTable(Connection $database): void
    {
        $database->executeStatement(
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
        $database->executeStatement('DROP TABLE IF EXISTS rank');
        self::createRankedListTable($database);
    }

    /**
     * Fetches a previously saved ranking. If a previous database is specified, ranking movements are calculated
     * relative to the previous ranking position.
     *
     * @param Connection $database
     * @param Ranking $ranking Ranking.
     * @param string|null $prevDb Optional. Path to a previous database.
     *
     * @return array Ranking of Steam apps.
     */
    public static function fetchRankedList(
        Connection $database,
        Ranking $ranking,
        string $prevDb = null,
        int $limit = null
    ): array {
        $sourceTable = $ranking instanceof Club250Ranking ? '(SELECT *, null owner FROM c250_ranking)' : 'rank';

        $query = $database->createQueryBuilder()
            ->select('rank.*, app.*, t250.rank as rank_250')
            ->from($sourceTable, 'rank')
            ->join('rank', 'app', 'app', 'id = rank.app_id')
            ->leftJoin('rank', 'rank', 't250', 't250.list_id = "top250" AND rank.app_id = t250.app_id')
            ->where('rank.list_id = :list_id')
                ->setParameter('list_id', $ranking->getId())
            ->orderBy('rank')
            ->setMaxResults($limit ?? $ranking->getLimit())
        ;

        if ($prevDb) {
            $database->executeStatement("ATTACH '$prevDb' AS prev");

            $query
                ->addSelect('prank.rank - rank.rank AS movement')
                ->leftJoin(
                    'rank',
                    preg_replace('[\w*?rank]', 'prev.$0', $sourceTable),
                    'prank',
                    'rank.list_id = prank.list_id AND
                        (rank.owner = prank.owner OR (rank.owner IS NULL AND rank.app_id = prank.app_id))'
                )
            ;
        }

        if ($ranking instanceof CustomRankingFetch) {
            $ranking->customizeRankingFetch($query);
        }

        $list = $query->execute()->fetchAllAssociative();

        $prevDb && $database->executeStatement('DETACH prev');

        return $list;
    }

    /**
     * Fetches a list of apps present in the previous database snapshot but "missing" from the current snapshot.
     */
    public static function fetchMissingApps(Connection $database, Ranking $ranking, int $limit, string $prevDb): array
    {
        $database->executeStatement("ATTACH '$prevDb' AS prev");

        try {
            return $database->executeQuery(
                'SELECT id, prank.rank, prank.owner, name, videos, video_manifest_hashes, capsule_hash, capsule_alt,
                    "dead" movement
                FROM prev.rank prank
                    JOIN prev.app ON id = prank.app_id
                    LEFT JOIN rank USING (list_id, app_id)
                WHERE prank.list_id = :ranking_id AND rank.app_id IS NULL
                ORDER BY prank.rank
                LIMIT :limit',
                [
                    'ranking_id' => $ranking->getId(),
                    'limit' => $limit,
                ]
            )->fetchAllAssociative();
        } finally {
            $database->executeStatement('DETACH prev');
        }
    }

    public static function fetchAppTags(Connection $database, int $appId): array
    {
        return $database->executeQuery(
            "SELECT id, name, category FROM app_tag JOIN tag ON id = tag_id
                WHERE app_id = $appId ORDER BY votes DESC"
        )->fetchAllAssociative();
    }

    public static function fetchPopularTags(Connection $database, int $threshold = 400): array
    {
        return $database->executeQuery("
            SELECT tag.id, tag.name, category
            FROM (
                SELECT tag.id, tag.name, category, COUNT(*) AS count
                FROM app_tag
                    JOIN (
                        SELECT app_id, AVG(votes) avg
                        FROM app_tag
                        GROUP BY app_id
                    )_ USING(app_id)
                    JOIN tag ON tag.id = tag_id
                    LEFT JOIN app ON app.id = app_id
                WHERE type = 'game' AND tag.name != 'VR' AND votes >= avg * .5
                GROUP BY tag.id
                HAVING count >= $threshold
                ORDER BY count DESC
                LIMIT 150
            ) tag LEFT JOIN tag_cat cat ON category = short_name
            ORDER BY cat.id, tag.name
        ")->fetchAllAssociative();
    }

    public static function countGames(Connection $database): int
    {
        return +$database->fetchOne("SELECT count(*) FROM app WHERE type = 'game'");
    }

    public static function countTags(Connection $database): int
    {
        return +$database->fetchOne("SELECT count(*) FROM tag");
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
            ->addSelect('app.*')
            ->from('app')
            ->leftJoin('app', 'app', 'parent', 'app.parent_id = parent.id')
            // Ensure platforms are defined to exclude discontinued apps.
            ->where('app.type = \'game\' AND app.platforms > 0 AND app.total_reviews > 0')
            // Exclude aliased games pointing to a valid parent.
            ->andWhere('(app.alias = 0 OR parent.id IS NULL)')
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
        $query->andWhere('app.id != 252150');
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
                RankingQueries::calculateBayesianScore($builder, $ranking->getWeight(), $prefix);
                break;

            case Algorithm::LAPLACE:
                RankingQueries::calculateLaplaceScore($builder, $ranking->getWeight(), $prefix);
                break;

            case Algorithm::LAPLACE_LOG:
                RankingQueries::calculateLaplaceLogScore($builder, $ranking->getWeight(), $prefix, $alias);
                break;

            case Algorithm::DIRICHLET_PRIOR:
                RankingQueries::calculateDirichletPriorScore($builder, $ranking->getWeight(), $prefix);
                break;

            case Algorithm::DIRICHLET_PRIOR_LOG:
                RankingQueries::calculateDirichletPriorLogScore($builder, $ranking->getWeight(), $prefix);
                break;

            case Algorithm::TORN:
                RankingQueries::calculateTornScore($builder, $ranking->getWeight());
                break;

            case Algorithm::HIDDEN_GEMS:
                RankingQueries::calculateHiddenGemsScore($builder, $ranking->getWeight(), $prefix);
                break;
        }
    }
}
