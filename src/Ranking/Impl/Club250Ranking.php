<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Generate\CustomizeGames;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Club250Ranking extends Top250Ranking implements CustomRankingFetch, CustomizeGames
{
    private const PATRON_REVIEW_AGGREGATION =
        '(
            SELECT *, COUNT(*) as total_reviews
            FROM patron_review AS pr
            INNER JOIN (
                SELECT app_id, COUNT(*) as positive_reviews
                FROM patron_review AS pr
                WHERE positive = 1
                GROUP BY pr.app_id
            ) AS pr2 ON pr.app_id = pr2.app_id
            GROUP BY pr.app_id
        )';

    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'club250/member_reviews');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->resetQueryPart('from')
            ->select(
                'app.id,
                pr.total_reviews,
                pr.positive_reviews,
                pr.total_reviews - pr.positive_reviews as negative_reviews'
            )
            ->from(self::PATRON_REVIEW_AGGREGATION, 'pr')
            ->leftJoin(
                'pr',
                'app',
                'app',
                'pr.app_id = app.id'
            )
            ->addOrderBy('score2', SortDirection::DESC)
        ;

        Queries::calculateScore($builder, $this, 'pr');
        Queries::calculateScore($builder, $this, 'app', 'score2');
    }

    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        // Override app review counts with patron review counts.
        $builder
            ->addSelect('pr.*, pr.total_reviews - pr.positive_reviews AS negative_reviews')
            ->innerJoin('rank', self::PATRON_REVIEW_AGGREGATION, 'pr', 'rank.app_id = pr.app_id')
        ;
    }

    public function customizeGames(array &$games, Connection $database): void
    {
        foreach ($games as &$game) {
            $game['reviews'] = Queries::fetchPatronReviews($database, +$game['id']);
        }
    }
}
