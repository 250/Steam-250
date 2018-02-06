<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class OwnersRanking extends AnnualList
{
    public function __construct(RankingDependencies $dependencies, int $year)
    {
        parent::__construct($dependencies, $year);

        $this->setId("owners/$year");
        $this->setTemplate('owners');
        $this->setAlgorithm(null);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        parent::customizeQuery($builder);

        $builder
            ->andWhere('price > 0')
            ->orderBy('owners', SortDirection::DESC)
        ;
    }
}
