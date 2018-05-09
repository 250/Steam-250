<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class DeveloperRanking extends Top250List implements CustomRankingFetch
{
    private $dependencies;

    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($this->dependencies = $dependencies, 'developer');

        $this->setWeight(2);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Score games for each developer using this ranking's algorithm and weights.
        $ownerScorer = Queries::createScorer($builder->getConnection(), $this);

        // Score individual games as if they were on the top 250 (used to pick most popular game).
        $gameScorer = Queries::createScorer($builder->getConnection(), new Top250List($this->dependencies));

        // More weight prefers more games.
        $weight = 1;

        $builder
            ->resetQueryParts()
            ->select('*, developer AS owner')
            ->addSelect(
                // Calculate Bayesian average (https://en.wikipedia.org/wiki/Bayesian_average) and scale scores up.
                "SIN(1.2 * (PI() *
                    -- Bayesian average.
                    (($weight * global_score_avg + score_sum) / ($weight + games))
                - .5 * PI())) * .5 + .5 AS score"
            )
            ->from(
                "(
                    SELECT app.id, app.name, app_developer.name AS developer,
                        -- MAX(game.score) forces the top scoring app ID into the aggregate row.
                        MAX(game.score), SUM(owner.score) AS score_sum, COUNT(*) AS games
                    FROM app
                    INNER JOIN app_developer ON app_id = app.id
                    INNER JOIN ($ownerScorer) owner ON owner.id = app.id
                    INNER JOIN ($gameScorer) game ON game.id = app.id
                    GROUP BY developer
                )"
            )
            ->from(
                "(
                    SELECT AVG(score) AS global_score_avg
                    FROM ($ownerScorer)
                )"
            )
            ->orderBy('score', SortDirection::DESC)
            ->setMaxResults($this->getLimit())
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
