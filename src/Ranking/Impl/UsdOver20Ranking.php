<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class UsdOver20Ranking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'price/over20');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('(app.discount_price IS NULL AND app.price > 2000) OR (app.discount_price > 2000)');
    }
}
