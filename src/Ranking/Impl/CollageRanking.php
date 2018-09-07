<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class CollageRanking extends Top250Ranking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'collage');
    }
}
