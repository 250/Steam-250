<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ThisQuarterList extends Top250List
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '90day', 50);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $time = strtotime('-3 month');
        $now = time();

        $builder
            ->andWhere("release_date > $time")
            // Some publishers list future dates.
            ->andWhere("release_date <= $now")
        ;
    }
}
