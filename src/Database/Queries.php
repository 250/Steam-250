<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Rank\RankingQueries;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

final class Queries
{
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
            self::calculateScore($query, $toplist->getAlgorithm(), $toplist->getWeight());
            $query->orderBy('score', SortDirection::DESC());
        }

        $toplist->customizeQuery($query);

        return $query->execute();
    }

    private static function calculateScore(QueryBuilder $builder, Algorithm $algorithm, float $weight): void
    {
        switch ($algorithm) {
            case Algorithm::WILSON:
                RankingQueries::calculateWilsonScore($builder, $weight);
                break;

            case Algorithm::BAYESIAN:
                RankingQueries::calculateBayesianScore($builder, $weight);
                break;

            case Algorithm::LAPLACE:
                RankingQueries::calculateLaplaceScore($builder, $weight);
                break;

            case Algorithm::LAPLACE_LOG:
                RankingQueries::calculateLaplaceLogScore($builder, $weight);
                break;

            case Algorithm::DIRICHLET_PRIOR:
                RankingQueries::calculateDirichletPriorScore($builder, $weight);
                break;

            case Algorithm::DIRICHLET_PRIOR_LOG:
                RankingQueries::calculateDirichletPriorLogScore($builder, $weight);
                break;

            case Algorithm::TORN:
                RankingQueries::calculateTornScore($builder, $weight);
                break;

            case Algorithm::HIDDEN_GEMS:
                RankingQueries::calculateHiddenGemsScore($builder, $weight);
                break;
        }
    }
}
