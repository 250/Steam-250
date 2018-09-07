<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Top250Ranking extends Ranking
{
    public function __construct(RankingDependencies $dependencies, $id = 'index', $limit = 250)
    {
        parent::__construct($dependencies, $id, $limit, Algorithm::LAPLACE_LOG(), .7);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Intentionally empty.
    }
}
