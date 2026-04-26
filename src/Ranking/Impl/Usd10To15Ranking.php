<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Usd10To15Ranking extends PriceRangeRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/10-15', 1000, 1500);

        $this->setTitle('Games from $10–15 Ranking');
        $this->setDescription('Top 250 best Steam games between $10 and $15.');
        $this->windowTitle = 'between ten and fifteen dollars';
    }
}
