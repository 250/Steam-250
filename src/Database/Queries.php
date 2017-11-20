<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use ScriptFUSION\Steam250\SiteGenerator\Algorithm;

final class Queries
{
    private const TOP_250_GAMES = 'SELECT * FROM rank NATURAL JOIN app WHERE algorithm = ? ORDER BY rank';

    public static function fetchTop250Games(Connection $database, Algorithm $algorithm, float $weight): Statement
    {
        return $database->executeQuery(self::TOP_250_GAMES, ["$algorithm$weight"]);
    }

    public static function fetchAppsSortedByScore(
        Connection $database,
        Algorithm $algorithm,
        float $weight
    ): Statement {
        return $database->executeQuery(
            'SELECT *, '
            . self::getQueryFragment($algorithm, $weight)
            . ' ORDER BY score DESC'
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
