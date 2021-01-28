<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\Shared\Platform;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Linux250Ranking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'linux250');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('app.platforms & ' . Platform::LINUX);
    }
}
