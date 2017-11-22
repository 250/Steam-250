<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class ThisQuarterList extends Toplist
{
    public function __construct()
    {
        parent::__construct('90day', Algorithm::LAPLACE_LOG(), 3, 50);
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
