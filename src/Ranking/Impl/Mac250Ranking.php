<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\Shared\Platform;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Mac250Ranking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'mac250');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('app.platforms & ' . Platform::MAC);
    }
}
