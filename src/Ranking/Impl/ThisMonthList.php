<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

class ThisMonthList extends Ranking
{
    public function __construct()
    {
        parent::__construct('30day', 40, Algorithm::LAPLACE_LOG(), 1);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $time = strtotime('last month');
        $now = time();

        $builder
            ->andWhere("release_date > $time")
            // Some publishers list future dates.
            ->andWhere("release_date <= $now")
        ;
    }
}
