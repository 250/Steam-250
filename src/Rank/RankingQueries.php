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
     * @param float $weight Optional. Lower numbers favour confidence over approval.
     *
     * @see https://math.stackexchange.com/a/41513
     */
    public static function calculateBayesianScore(QueryBuilder $builder, float $weight = 1): void
    {
        $builder->addSelect(
            "CASE
                WHEN (total_reviews * $weight * 1. / agg.max_votes) > 1
                THEN (positive_reviews * 1. / total_reviews)
                ELSE (total_reviews * $weight * 1. / agg.max_votes) * (positive_reviews * 1. / total_reviews)
                    + (1 - (total_reviews * $weight * 1. / agg.max_votes)) * agg.avg_score
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
     * @param float $weight
     *
     * @see http://planspace.org/2014/08/17/how-to-sort-by-average-rating/
     */
    public static function calculateLaplaceScore(QueryBuilder $builder, float $weight = 1): void
    {
        $builder->addSelect(
            "(positive_reviews + $weight) / (total_reviews + $weight * 2.) AS score"
        );
    }

    public static function calculateLaplaceLogScore(QueryBuilder $builder, float $weight): void
    {
        $builder->addSelect(
            "(
                positive_reviews * 1. / total_reviews * LOG10(total_reviews + 1) + $weight
            ) / (LOG10(total_reviews + 1) + $weight * 2.) AS score"
        );
    }

    /**
     * @param QueryBuilder $builder
     * @param float $weight
     *
     * @see http://www.dcs.bbk.ac.uk/%7Edell/publications/dellzhang_ictir2011.pdf
     */
    public static function calculateDirichletPriorScore(QueryBuilder $builder, float $weight): void
    {
        $builder->addSelect("(positive_reviews + $weight * p) / (total_reviews + $weight) AS score")
            ->from('(SELECT SUM(positive_reviews) * 1. / SUM(total_reviews) AS p FROM app)')
        ;
    }

    public static function calculateDirichletPriorLogScore(QueryBuilder $builder, float $weight): void
    {
        $builder->addSelect(
            "(
                (positive_reviews * 1. / total_reviews) * LOG10(total_reviews + 1) + $weight * p)
                   / (LOG10(total_reviews + 1) + $weight
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
     * @param float $weight Optional. Weighting. Default value is given by a previous optimization: pow(10, 6.42).
     * @param float $wilson_weight Optional. Z value derived from confidence level (see probability table).
     *      Default value represents 95% confidence.
     *
     * @see https://github.com/woctezuma/hidden-gems
     */
    public static function calculateHiddenGemScore(QueryBuilder $builder, float $weight = 2630000.0, float $wilson_weight = 1.96): void
    {
        $builder->addSelect(
            "wilson_score * ($weight * 1. / ($weight + players))
                AS score"
        )->from(
            "(
                (positive_reviews + POWER($wilson_weight, 2) / 2.) / total_reviews - $wilson_weight
                    * SQRT((positive_reviews * negative_reviews) / total_reviews + POWER($wilson_weight, 2) / 4.)
                    / total_reviews
            ) / (1 + POWER($wilson_weight, 2) * 1. / total_reviews) AS wilson_score"
        );
    }    
}
