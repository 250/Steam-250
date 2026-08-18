<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Bottom100Ranking extends Ranking implements StaticId
{
    public function __construct(RankingDependencies $dependencies, float $weight = 4000)
    {
        parent::__construct($dependencies, 100, Algorithm::BAYESIAN(), $weight);
    }

    public static function getStaticId(): string
    {
        return 'bottom100';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return $builder->orderBy('score', SortDirection::ASC);
    }
}
