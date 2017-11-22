<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class ThisMonthList extends Toplist
{
    public function __construct()
    {
        parent::__construct('30day', Algorithm::LAPLACE_LOG(), 4, 25);
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
