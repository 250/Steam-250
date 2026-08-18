<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Usd5To10Ranking extends PriceRangeRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 500, 1000);

        $this->setTitle('Games from $5–10 Ranking');
        $this->setDescription('Top 250 best Steam games between $5 and $10.');
        $this->windowTitle = 'between five and ten dollars';
    }

    public static function getStaticId(): string
    {
        return 'price/5-10';
    }
}
