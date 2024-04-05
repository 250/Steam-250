<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class TrendRanking extends Club250Ranking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'TREND', 1000);

        $this->setTitle('New and Trending');
        $this->setDescription(
            'Top trending Steam games released within the last 30 days, based on reviews per day since release.'
        );
    }

    public function getUrl(): string
    {
        return "$_ENV[CLUB_250_BASE_URL]/ranking/new-and-trending";
    }
}
