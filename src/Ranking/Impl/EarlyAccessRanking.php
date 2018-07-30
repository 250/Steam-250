<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class EarlyAccessRanking extends TagRanking
{
    public const TAG = 'Early Access';

    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, self::TAG);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('ea = 1');
    }
}
