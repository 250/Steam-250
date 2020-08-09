<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

abstract class DevlisherRanking extends Ranking implements CustomRankingFetch
{
    private RankingDependencies $dependencies;

    // More weight prefers more games.
    private float $bayesianWeight;

    public function __construct(RankingDependencies $dependencies, string $mode, float $bayesianWeight)
    {
        parent::__construct($this->dependencies = $dependencies, $mode, 250, Algorithm::LAPLACE_LOG(), 2.);

        $this->bayesianWeight = $bayesianWeight;

        $this->setTemplate('devlisher');
    }

    public function export(): array
    {
        $export = parent::export();

        // Copy owner key to developer/publisher key to be picked up by risers macro.
        isset($export['missing']) &&
            array_walk($export['missing'], fn (&$missing) => $missing[$this->getId()] = $missing['owner']);

        return $export;
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Score games for each developer using this ranking's algorithm and weights.
        $ownerScorer = Queries::createScorer($builder->getConnection(), $this);

        // Score individual games as if they were on the top 250 (used to pick most popular game).
        $gameScorer = Queries::createScorer($builder->getConnection(), new Top250Ranking($this->dependencies));

        $mode = $this->getId();

        $builder
            ->resetQueryParts()
            ->select("*, $mode AS owner")
            ->addSelect(
                // Calculate Bayesian average (https://en.wikipedia.org/wiki/Bayesian_average) and scale scores up.
                "SIN(1.2 * (PI() *
                    -- Bayesian average.
                    (($this->bayesianWeight * global_score_avg + score_sum) / ($this->bayesianWeight + games))
                - .5 * PI())) * .5 + .5 AS score"
            )
            ->from(
                "(
                    SELECT app.id, app.name, app_$mode.name AS $mode,
                        -- MAX(game.score) forces the top scoring app ID into the aggregate row.
                        MAX(game.score), SUM(owner.score) AS score_sum, COUNT(*) AS games
                    FROM app
                    INNER JOIN app_$mode ON app_id = app.id
                    INNER JOIN ($ownerScorer) owner ON owner.id = app.id
                    INNER JOIN ($gameScorer) game ON game.id = app.id
                    GROUP BY $mode
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
        $mode = $this->getId();

        $builder
            ->addSelect('agg.*')
            ->innerJoin(
                'rank',
                "(
                    SELECT app_$mode.name AS $mode, COUNT(*) AS games,
                        -- Override review totals with developer aggregate totals.
                        SUM(total_reviews) AS total_reviews, SUM(positive_reviews) AS positive_reviews
                    FROM app
                    INNER JOIN app_$mode ON app_id = id
                    WHERE type = 'game' AND platforms > 0
                    GROUP BY $mode
                )",
                'agg',
                "agg.$mode = rank.owner"
            )
        ;
    }
}
