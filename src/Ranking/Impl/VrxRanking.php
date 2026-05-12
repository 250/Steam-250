<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class VrxRanking extends VrTop250Ranking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies);

        $this->setId('vr_exclusives');
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        parent::customizeQuery($builder);

        return $builder->andWhere('app.vrx = 1');
    }
}
