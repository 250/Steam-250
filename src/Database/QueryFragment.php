<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use ScriptFUSION\StaticClass;

final class QueryFragment
{
    use StaticClass;

    /**
     * Wilson: approval <=> votes.
     *
     * @param float $weight Optional. Z value derived from confidence level (see probability table). Default value
     *     represents 95% confidence.
     *
     * @return string
     *
     * @see http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
     * @see https://en.wikipedia.org/wiki/Checking_whether_a_coin_is_fair#Estimator_of_true_probability
     */
    public static function calculateWilsonScore(float $weight = 1.96): string
    {
        return
            "(
                (positive_reviews + POWER($weight, 2) / 2.) / total_reviews - $weight
                    * SQRT((positive_reviews * negative_reviews) / total_reviews + POWER($weight, 2) / 4.)
                    / total_reviews
            ) / (1 + POWER($weight, 2) * 1. / total_reviews) AS score
            FROM app"
        ;
    }

    /**
     * Bayesian: votes <=> approval.
     *
     * @param float $weight Optional. Lower numbers favour confidence over approval.
     *
     * @return string
     */
    public static function calculateBayesianScore(float $weight = 1): string
    {
        return
             "CASE 
                 WHEN (total_reviews * $weight * 1. / agg.max_votes) > 1
                 THEN (positive_reviews * 1. / total_reviews)
                 ELSE (total_reviews * $weight * 1. / agg.max_votes) * (positive_reviews * 1. / total_reviews)
                    + (1 - (total_reviews * $weight * 1. / agg.max_votes)) * agg.avg_score
             END score
             FROM app,
                (
                    SELECT 
                        AVG(positive_reviews * 1. / total_reviews) AS avg_score,
                        MAX(total_reviews) AS max_votes
                    FROM app
                ) agg"
        ;
    }

    /**
     * Laplace: approval <=> votes.
     *
     * @param float $weight
     *
     * @return string
     */
    public static function calculateLaplaceScore(float $weight): string
    {
        return
            "(positive_reviews + $weight) / (total_reviews + $weight * 2.) AS score
            FROM app"
        ;
    }

    public static function calculateLaplaceLogScore(float $weight): string
    {
        return
            "(
                positive_reviews * 1. / total_reviews * LOG10(total_reviews + 1) + $weight
            ) / (LOG10(total_reviews + 1) + $weight * 2.) AS score
            FROM app"
        ;
    }

    public static function calculateDirichletPriorScore(float $weight): string
    {
        return
            "(positive_reviews + $weight * p) / (total_reviews + $weight) AS score
            FROM app, (SELECT SUM(positive_reviews) * 1. / SUM(total_reviews) AS p FROM app)"
        ;
    }

    public static function calculateDirichletPriorLogScore(float $weight): string
    {
        return
            "(
                (positive_reviews * 1. / total_reviews) * LOG10(total_reviews + 1) + $weight * p)
                   / (LOG10(total_reviews + 1) + $weight
            ) AS score
            FROM app, (SELECT SUM(positive_reviews) * 1. / SUM(total_reviews) AS p FROM app)"
        ;
    }

    /**
     * @param float $weight Optional. Weighting. Default value is given by log(10, 2).
     *
     * @return string
     */
    public static function calculateTornScore(float $weight = 3.3219280948874): string
    {
        return
            "(positive_reviews * 1. / total_reviews) -
                ((positive_reviews * 1. / total_reviews) - .5) * POWER(2, -LOG(total_reviews + 1, POWER(2, $weight)))
                AS score
            FROM app"
        ;
    }
}
