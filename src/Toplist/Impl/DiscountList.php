<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;

class DiscountList extends Top250List
{
    public function __construct()
    {
        parent::__construct('discounts');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('discount > 0');
    }
}
