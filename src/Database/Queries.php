<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\StaticClass;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Rank\RankingQueries;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

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
     * Fetches a previously saved ranked list. If a previous database is specified, ranking movements are calculated
     * relative to the previous ranking.
     *
     * @param Connection $database
     * @param Toplist $toplist List.
     * @param string $prevDb Optional. Path to a previous database.
     *
     * @return array Ranked list of Steam apps.
     */
    public static function fetchRankedList(Connection $database, Toplist $toplist, string $prevDb = null): array
    {
        $query = $database->createQueryBuilder()
            ->select('rank.*, app.*, t250.rank as rank_250')
            ->from('rank')
            ->join('rank', 'app', 'app', 'id = rank.app_id')
            ->leftJoin('rank', 'rank', 't250', 't250.list_id = "index" AND rank.app_id = t250.app_id')
            ->where('rank.list_id = :list_id')
                ->setParameter('list_id', $toplist->getId())
            ->orderBy('rank')
            ->setMaxResults($toplist->getLimit())
        ;

        if ($prevDb) {
            $database->exec("ATTACH '$prevDb' AS prev");

            $query
                ->addSelect('prank.rank - rank.rank AS movement')
                ->leftJoin(
                    'rank',
                    'prev.rank',
                    'prank',
                    'rank.list_id = prank.list_id AND rank.app_id = prank.app_id'
                )
            ;
        }

        if ($toplist instanceof CustomRankingFetch) {
            $toplist->customizeRankingFetch($query);
        }

        $list = $query->execute()->fetchAll();

        $prevDb && $database->exec('DETACH prev');

        return $list;
    }

    public static function fetchAppTags(Connection $database, int $appId): array
    {
        return $database->query("SELECT tag FROM app_tag WHERE app_id = $appId ORDER BY `index`")
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function fetchPopularTags(Connection $database, int $threshold = 500): array
    {
        return $database->query(
            "SELECT tag, COUNT(tag) AS count
            FROM app_tag
            WHERE tag != 'VR'
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
     * @param Toplist $toplist List.
     *
     * @return Statement
     */
    public static function rankList(Connection $database, Toplist $toplist): Statement
    {
        $query = $database->createQueryBuilder()
            ->select('*')
            ->from('app')
            ->where('type = \'game\'')
            ->setMaxResults($toplist->getLimit())
        ;

        if ($toplist->getAlgorithm()) {
            self::calculateScore($query, $toplist);
            $query->orderBy('score', SortDirection::DESC);
        }

        $toplist->customizeQuery($query);

        return $query->execute();
    }

    public static function calculateScore(
        QueryBuilder $builder,
        Toplist $toplist,
        string $prefix = 'app',
        string $alias = 'score'
    ): void {
        switch ($toplist->getAlgorithm()) {
            case Algorithm::WILSON:
                RankingQueries::calculateWilsonScore($builder, $toplist->getWeight());
                break;

            case Algorithm::BAYESIAN:
                RankingQueries::calculateBayesianScore($builder, $toplist->getWeight());
                break;

            case Algorithm::LAPLACE:
                RankingQueries::calculateLaplaceScore($builder, $toplist->getWeight());
                break;

            case Algorithm::LAPLACE_LOG:
                RankingQueries::calculateLaplaceLogScore($builder, $toplist->getWeight(), $prefix, $alias);
                break;

            case Algorithm::DIRICHLET_PRIOR:
                RankingQueries::calculateDirichletPriorScore($builder, $toplist->getWeight());
                break;

            case Algorithm::DIRICHLET_PRIOR_LOG:
                RankingQueries::calculateDirichletPriorLogScore($builder, $toplist->getWeight());
                break;

            case Algorithm::TORN:
                RankingQueries::calculateTornScore($builder, $toplist->getWeight());
                break;

            case Algorithm::HIDDEN_GEMS:
                RankingQueries::calculateHiddenGemsScore($builder, $toplist->getWeight());
                break;
        }
    }
}
