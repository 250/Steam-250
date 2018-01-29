<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;

class UsdOver15List extends Top250List
{
    public function __construct()
    {
        parent::__construct('price/over15');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('(discount_price IS NULL AND price > 1500) OR (discount_price > 1500)');
    }
}
