<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Usd10To15List extends PriceRangeRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/10-15', 1000, 1500);
    }
}
