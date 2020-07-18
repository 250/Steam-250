<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

/**
 * Specifies a default algorithm and weighting suitable for most rankings.
 */
abstract class DefaultRanking extends Ranking
{
    public function __construct(RankingDependencies $dependencies, string $id, int $limit = 250)
    {
        parent::__construct($dependencies, $id, $limit, Algorithm::LAPLACE_LOG(), .75);
    }
}
