<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

abstract class RollingRanking extends DefaultRanking
{
    private string $date;

    public function __construct(
        RankingDependencies $dependencies,
        string $id,
        string $date,
        protected(set) string $periodName,
        protected(set) int $days,
        int $limit = 250,
    ) {
        parent::__construct($dependencies, $id, $limit);

        $this->setTemplate('rolling');
        $this->date = $date;
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $time = strtotime($this->date);
        $now = time();

        $builder
            ->andWhere("app.release_date > $time")
            // Some publishers list future dates.
            ->andWhere("app.release_date <= $now")
        ;
    }
}
