<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class TrendRanking extends Club250Ranking implements CustomRankingFetch, StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 1000);

        $this->setTitle('New and Trending');
        $this->setDescription(
            'Top trending Steam games released within the last 30 days, based on reviews per day since release.'
        );
    }

    public static function getStaticId(): string
    {
        return 'TREND';
    }

    public function getUrl(): string
    {
        return "$_ENV[CLUB_250_BASE_URL]/ranking/trending-now";
    }

    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        $builder
            ->addSelect('(SELECT name FROM app_developer WHERE app_id = app.id ORDER BY "order" LIMIT 1) developer')
            ->groupBy('app.id')
        ;
    }
}
