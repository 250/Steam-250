<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class UsdOver15Ranking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/over15');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('(discount_price IS NULL AND price > 1500) OR (discount_price > 1500)');
    }
}
