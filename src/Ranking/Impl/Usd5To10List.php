<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Usd5To10List extends PriceRangeList
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/5-10', 500, 1000);
    }
}
