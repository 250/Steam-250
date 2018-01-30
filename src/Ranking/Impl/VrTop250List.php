<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;
use ScriptFUSION\Top250\Shared\Platform;

class VrTop250List extends Top250List
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'vr250');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('platforms & ' . (Platform::VIVE | Platform::OCCULUS | Platform::WMR));
    }
}
