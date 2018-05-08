<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class DeveloperRanking extends Top250List implements CustomRankingFetch
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'developer');

        $this->setWeight(2);
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

        // More weight prefers more games.
        $weight = 1;

        $builder
            // Calculate Bayesian average. https://en.wikipedia.org/wiki/Bayesian_average
            ->select("*, ($weight * global_score_avg + score_sum) / ($weight + games) AS score, developer AS owner")
            ->from(
                "(
                    SELECT app.id, app.name, app_developer.name AS developer,
                        -- MAX(score) forces the top scoring app ID into the aggregate row.
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

    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        $builder
            ->addSelect('agg.*')
            ->innerJoin(
                'rank',
                '(
                    SELECT app_developer.name AS developer, COUNT(*) AS games,
                        -- Override review totals with developer aggregate totals.
                        SUM(total_reviews) AS total_reviews, SUM(positive_reviews) AS positive_reviews
                    FROM app
                    INNER JOIN app_developer ON app_id = id
                    WHERE type = \'game\' AND platforms > 0
                    GROUP BY developer
                )',
                'agg',
                'agg.developer = rank.owner'
            )
        ;
    }
}
