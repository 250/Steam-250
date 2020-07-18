<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class RollingWeekRanking extends RollingRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '7day', '-7 day', 25);

        $this->setWeight(4);
        $this->setTitle('Week Top 25');
        $this->setDescription('Top 25 best Steam games released in the last 7 days, according to gamer reviews.');
    }
}
