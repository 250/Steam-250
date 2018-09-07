<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

abstract class RollingRanking extends Top250Ranking
{
    private $date;

    public function __construct(RankingDependencies $dependencies, string $id, string $date, int $limit = 250)
    {
        parent::__construct($dependencies, $id, $limit);

        $this->date = $date;
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $time = strtotime($this->date);
        $now = time();

        $builder
            ->andWhere("release_date > $time")
            // Some publishers list future dates.
            ->andWhere("release_date <= $now")
        ;
    }
}
