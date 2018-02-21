<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ThisWeekRanking extends RollingRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '7day', '-7 day', 20);

        $this->setWeight(4);
    }
}
