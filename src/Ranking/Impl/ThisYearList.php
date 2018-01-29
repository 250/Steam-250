<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;

class ThisYearList extends Top250List
{
    public function __construct()
    {
        parent::__construct('365day', 100);
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
