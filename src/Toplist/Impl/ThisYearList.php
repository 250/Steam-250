<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class ThisYearList extends Toplist
{
    public function __construct()
    {
        parent::__construct('365day', Algorithm::LAPLACE_LOG(), 1.4, 100);
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
