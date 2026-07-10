<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Rank\PrecomputedRankingTable;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class GlobalTopSellersRanking extends DefaultRanking implements PrecomputedRankingTable, CustomRankingFetch
{
    public function __construct(RankingDependencies $dependencies, $id = 'global_top_sellers', $limit = 100)
    {
        parent::__construct($dependencies, $id, $limit);

        $this->setTitle('Global Top Sellers');
        $this->setDescription('Top selling games worldwide right now.');
    }

    public function getSourceTable(): string
    {
        return 'global_top_sellers';
    }

    public function getUrl(): string
    {
        return "$_ENV[CLUB_250_BASE_URL]/ranking/global-top-sellers";
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return null;
    }

    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        $builder
            ->andWhere("app.type = 'game'")
            // Exclude free games released over a year ago.
            ->andWhere(
                "NOT (app.free = 1 AND app.release_date < strftime('%s', 'now', '-1 year'))"
            )
            ->addSelect('dev.name developer')
            ->leftJoin('app', 'app_developer', 'dev', 'dev.app_id = app.id')
            ->groupBy('app.id')
        ;
    }
}
