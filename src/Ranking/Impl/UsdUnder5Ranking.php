<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class UsdUnder5Ranking extends PriceRangeRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 0, 500);

        $this->setTitle('Games under $5 Ranking');
        $this->setDescription('Top 250 best Steam games for $5 or less, perfect for bargain hunters.');
        $this->windowTitle = 'under five dollars';
    }

    public static function getStaticId(): string
    {
        return 'price/under5';
    }
}
