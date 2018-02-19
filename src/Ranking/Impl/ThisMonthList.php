<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ThisMonthList extends RollingRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '30day', 'last month', 40);

        $this->setWeight(1);
    }
}
