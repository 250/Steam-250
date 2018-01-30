<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class UsdUnder5List extends PriceRangeList
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/under5', 0, 500);
    }
}
