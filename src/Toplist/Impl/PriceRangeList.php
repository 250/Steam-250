<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;

abstract class PriceRangeList extends Top250List
{
    private $min;
    private $max;

    public function __construct(string $id, int $min, int $max)
    {
        parent::__construct($id);

        $this->min = $min;
        $this->max = $max;
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere(
            "(discount_price IS NULL AND price > $this->min AND price <= $this->max)
            OR (discount_price > $this->min AND discount_price <= $this->max)"
        );
    }
}
