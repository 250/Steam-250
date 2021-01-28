<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\StaticClass;

final class RankingQueries
{
    use StaticClass;

    /**
     * Wilson: approval <=> votes.
     *
     * @param QueryBuilder $builder
     * @param float $weight Optional. Z value derived from confidence level (see probability table). Default value
     *     represents 95% confidence.
     *
     * @see http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
     * @see https://en.wikipedia.org/wiki/Normal_distribution#Quantile_function
     */
    public static function calculateWilsonScore(QueryBuilder $builder, float $weight = 1.96): void
    {
        $builder->addSelect(
            "(
                (positive_reviews + POWER($weight, 2) / 2.) / total_reviews - $weight
                    * SQRT((positive_reviews * negative_reviews) / total_reviews + POWER($weight, 2) / 4.)
                    / total_reviews
            ) / (1 + POWER($weight, 2) * 1. / total_reviews) AS score"
        );
    }

    /**
     * Bayesian: votes <=> approval.
     *
     * @param QueryBuilder $builder
     * @param float $weight Optional. Lower numbers favour confidence over approval. Default 1.
     *
     * @see https://math.stackexchange.com/a/41513
     */
    public static function calculateBayesianScore(QueryBuilder $builder, float $weight, string $prefix): void
    {
        $builder->addSelect(
            "CASE
                WHEN ($prefix.total_reviews * $weight * 1. / agg.max_votes) > 1
                THEN ($prefix.positive_reviews * 1. / $prefix.total_reviews)
                ELSE (
                    $prefix.total_reviews * $weight * 1. / agg.max_votes)
                    * ($prefix.positive_reviews * 1. / $prefix.total_reviews
                ) + (1 - ($prefix.total_reviews * $weight * 1. / agg.max_votes)) * agg.avg_score
            END score"
        )->from(
            '(
                SELECT 
                    AVG(positive_reviews * 1. / total_reviews) AS avg_score,
                    MAX(total_reviews) AS max_votes
                FROM app
            ) agg'
        );
    }

    /**
     * Laplace: approval <=> votes.
     *
     * @param QueryBuilder $builder
     * @param float $weight Default 1.
     *
     * @see http://planspace.org/2014/08/17/how-to-sort-by-average-rating/
     */
    public static function calculateLaplaceScore(QueryBuilder $builder, float $weight, string $prefix): void
    {
        $builder->addSelect(
            "($prefix.positive_reviews + $weight) / ($prefix.total_reviews + $weight * 2.) AS score"
        );
    }

    public static function calculateLaplaceLogScore(
        QueryBuilder $builder,
        float $weight,
        string $prefix,
        string $alias
    ): void {
        $builder->addSelect(
            "(
                $prefix.positive_reviews * 1. / $prefix.total_reviews * LOG10($prefix.total_reviews + 1) + $weight
            ) / (LOG10($prefix.total_reviews + 1) + $weight * 2.) AS $alias"
        );
    }

    /**
     * @param QueryBuilder $builder
     * @param float $weight
     *
     * @see http://www.dcs.bbk.ac.uk/%7Edell/publications/dellzhang_ictir2011.pdf
     */
    public static function calculateDirichletPriorScore(QueryBuilder $builder, float $weight, string $prefix): void
    {
        $builder->addSelect("($prefix.positive_reviews + $weight * p) / ($prefix.total_reviews + $weight) AS score")
            ->from("(SELECT SUM(positive_reviews) * 1. / SUM(total_reviews) AS p FROM app)")
        ;
    }

    public static function calculateDirichletPriorLogScore(QueryBuilder $builder, float $weight, string $prefix): void
    {
        $builder->addSelect(
            "(
                ($prefix.positive_reviews * 1. / $prefix.total_reviews)
                * LOG10($prefix.total_reviews + 1) + $weight * p)
                   / (LOG10($prefix.total_reviews + 1) + $weight
            ) AS score"
        )->from('(SELECT SUM(positive_reviews) * 1. / SUM(total_reviews) AS p FROM app)');
    }

    /**
     * @param QueryBuilder $builder
     * @param float $weight Optional. Weighting. Default value is given by log(10, 2).
     *
     * @see https://steamdb.info/blog/steamdb-rating/
     */
    public static function calculateTornScore(QueryBuilder $builder, float $weight = 3.3219280948874): void
    {
        $builder->addSelect(
            "(positive_reviews * 1. / total_reviews) -
                ((positive_reviews * 1. / total_reviews) - .5) * POWER(2, -LOG(total_reviews + 1, POWER(2, $weight)))
                AS score"
        );
    }

    /**
     * @param QueryBuilder $builder
     * @param float $weight Weighting.
     *
     * @see https://github.com/woctezuma/hidden-gems
     */
    public static function calculateHiddenGemsScore(QueryBuilder $builder, float $weight, string $prefix): void
    {
        $wilsonWeight = 1.96;

        $builder->addSelect(
            "(
                (
                    ($prefix.positive_reviews + POWER($wilsonWeight, 2) / 2.) / $prefix.total_reviews - $wilsonWeight
                        * SQRT(
                            ($prefix.positive_reviews * $prefix.negative_reviews)
                            / $prefix.total_reviews + POWER($wilsonWeight, 2) / 4.
                        ) / $prefix.total_reviews
                ) / (1 + POWER($wilsonWeight, 2) * 1. / $prefix.total_reviews)
            ) * ($weight * 1. / ($weight + $prefix.total_reviews)) AS score"
        )
            ->leftJoin($prefix, 'app_tag', 'app_tag', "$prefix.id = app_tag.app_id AND tag = 'Visual Novel'")
            ->join(
                'app',
                '(
                    SELECT app_id, AVG(votes) as avg
                    FROM app_tag
                    GROUP BY app_id
                )',
                'avg',
                "$prefix.id = avg.app_id"
            )
        ;
    }
}
