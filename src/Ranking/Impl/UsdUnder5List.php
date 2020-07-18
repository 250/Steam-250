<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class UsdUnder5List extends PriceRangeRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/under5', 0, 500);

        $this->setTitle('Under $5 250');
        $this->setDescription('Top 250 best Steam games for $5 or less, perfect for bargain hunters.');
    }
}
