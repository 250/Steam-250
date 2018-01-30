<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ThisYearList extends Top250List
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '365day', 100);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $time = strtotime('last year');
        $now = time();

        $builder
            ->andWhere("release_date > $time")
            // Some publishers list future dates.
            ->andWhere("release_date <= $now")
        ;
    }
}
