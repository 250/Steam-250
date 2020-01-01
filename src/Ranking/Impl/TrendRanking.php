<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class TrendRanking extends Ranking implements CustomRankingFetch
{
    public const DAYS = 50;

    private const VELOCITY_QUERY =
        'total_reviews / CASE
            -- Penalize apps with no release date by 1 year.
            WHEN release_date IS NULL THEN 365

            -- Number of days since release.
            ELSE MAX(1, ABS(JULIANDAY(CURRENT_DATE) - JULIANDAY(DATETIME(release_date, "unixepoch")))) 
        END reviews_per_day'
    ;

    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'trending', 50);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->addSelect('DATETIME(release_date, "unixepoch") AS release_date')
            ->addSelect('JULIANDAY(CURRENT_DATE) - JULIANDAY(DATETIME(release_date, "unixepoch")) AS days')
            ->addSelect(self::VELOCITY_QUERY)
            ->andWhere('total_reviews > 0')
            ->andWhere('release_date <= CURRENT_DATE')
            ->andWhere('days < ' . self::DAYS)
            ->orderBy('reviews_per_day', SortDirection::DESC)
        ;
    }

    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        $builder->addSelect(self::VELOCITY_QUERY);
    }
}
