<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

abstract class PriceRangeRanking extends DefaultRanking
{
    private int $min;
    private int $max;

    public function __construct(RankingDependencies $dependencies, string $id, int $min, int $max)
    {
        parent::__construct($dependencies, $id);

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
