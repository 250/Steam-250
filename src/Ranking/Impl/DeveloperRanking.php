<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class DeveloperRanking extends Top250List
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'developers');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $limit = $builder->getMaxResults();

        // Convert current builder into scorer and clear it ready for wrapping in outer query.
        $scorer = clone $builder
            ->setMaxResults(null)
            ->resetQueryPart('orderBy')
        ;
        $builder->resetQueryParts();

        $builder
            // Calculate Bayesian average. https://en.wikipedia.org/wiki/Bayesian_average
            ->select('*, (global_score_avg + score_sum) / (1 + games) AS score')
            ->from(
                "(
                    SELECT app.id, app.name, app_developer.name AS developer,
                        MAX(score), SUM(score) AS score_sum, COUNT(*) AS games
                    FROM app
                    INNER JOIN app_developer ON app_id = app.id
                    INNER JOIN ($scorer) self ON self.id = app.id
                    GROUP BY developer
                )"
            )
            ->from(
                "(
                    SELECT AVG(score) AS global_score_avg
                    FROM ($scorer)
                )"
            )
            ->orderBy('score', SortDirection::DESC)
            ->setMaxResults($limit)
        ;
    }
}
