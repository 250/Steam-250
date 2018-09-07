<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class RollingYearRanking extends RollingRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '365day', 'last year', 150);
    }
}
