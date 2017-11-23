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
    private const RANKED_LIST = 'SELECT * FROM rank JOIN app ON id = app_id WHERE list_id = ? ORDER BY rank LIMIT ?';

    /**
     * Fetches a previously saved ranked list.
     *
     * @param Connection $database
     * @param Toplist $toplist List.
     *
     * @return Statement
     */
    public static function fetchRankedList(Connection $database, Toplist $toplist): Statement
    {
        return $database->executeQuery(self::RANKED_LIST, [$toplist->getTemplate(), $toplist->getLimit()]);
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
