<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class DiscountRanking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'discounts');

        $this->setTitle('Discount 250');
        $this->setDescription('Top 250 best Steam games currently on sale, according to gamer reviews.');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('app.discount > 0');
    }
}
