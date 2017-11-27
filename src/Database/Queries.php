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
            ->select('rank.*, app.*')
            ->from('rank')
            ->join('rank', 'app', 'app', 'id = rank.app_id')
            ->where("rank.list_id = '{$toplist->getTemplate()}'")
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
            ->orderBy('score', $toplist->getDirection())
            ->setMaxResults($toplist->getLimit())
        ;

        self::calculateScore($query, $toplist->getAlgorithm(), $toplist->getWeight());
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
        }
    }
}
