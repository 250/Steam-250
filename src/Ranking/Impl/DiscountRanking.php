<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class DiscountRanking extends Top250Ranking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'discounts');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('discount > 0');
    }
}
