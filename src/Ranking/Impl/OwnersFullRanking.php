<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class OwnersFullRanking extends Ranking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'owners', 250);

        $this->setTemplate('owners_full');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->andWhere('price > 0')
            ->orderBy('owners', SortDirection::DESC)
        ;
    }
}
