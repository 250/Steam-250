<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Usd15To20Ranking extends PriceRangeRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/15-20', 1500, 2000);

        $this->setTitle('Games from $15–20 Ranking');
        $this->setDescription('Top 250 best Steam games between $15 and $20.');
        $this->windowTitle = 'between fifteen and twenty dollars';
    }
}
