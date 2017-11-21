<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

final class Queries
{
    private const RANKED_LIST = 'SELECT * FROM rank NATURAL JOIN app WHERE list_id = ? ORDER BY rank';

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
        return $database->executeQuery(self::RANKED_LIST, [$toplist->getTemplate()]);
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
        return $database->executeQuery(
            'SELECT *, '
            . self::getQueryFragment($toplist->getAlgorithm(), $toplist->getWeight())
            . " WHERE app_type = 'game' ORDER BY score {$toplist->getDirection()} LIMIT {$toplist->getLimit()}"
        );
    }

    private static function getQueryFragment(Algorithm $algorithm, float $weight): string
    {
        switch ($algorithm) {
            case Algorithm::WILSON:
                return QueryFragment::calculateWilsonScore($weight);

            case Algorithm::BAYESIAN:
                return QueryFragment::calculateBayesianScore($weight);

            case Algorithm::LAPLACE:
                return QueryFragment::calculateLaplaceScore($weight);

            case Algorithm::LAPLACE_LOG:
                return QueryFragment::calculateLaplaceLogScore($weight);

            case Algorithm::DIRICHLET_PRIOR:
                return QueryFragment::calculateDirichletPriorScore($weight);

            case Algorithm::DIRICHLET_PRIOR_LOG:
                return QueryFragment::calculateDirichletPriorLogScore($weight);

            case Algorithm::TORN:
                return QueryFragment::calculateTornScore($weight);
        }
    }
}
