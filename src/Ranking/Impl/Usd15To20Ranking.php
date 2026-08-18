<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Usd15To20Ranking extends PriceRangeRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 1500, 2000);

        $this->setTitle('Games from $15–20 Ranking');
        $this->setDescription('Top 250 best Steam games between $15 and $20.');
        $this->windowTitle = 'between fifteen and twenty dollars';
    }

    public static function getStaticId(): string
    {
        return 'price/15-20';
    }
}
