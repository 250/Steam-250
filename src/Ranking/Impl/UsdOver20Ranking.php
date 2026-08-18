<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class UsdOver20Ranking extends PriceRangeRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 0, 0);

        $this->setTitle('Games over $20 Ranking');
        $this->setDescription('Top 250 best Steam games over $20.');
        $this->windowTitle = 'over twenty dollars';
    }

    public static function getStaticId(): string
    {
        return 'price/over20';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return $builder->andWhere('(app.discount_price IS NULL AND app.price > 2000) OR (app.discount_price > 2000)');
    }
}
