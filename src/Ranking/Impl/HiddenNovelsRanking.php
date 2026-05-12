<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class HiddenNovelsRanking extends HiddenGemsRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'hidden_novels');
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        // Include only visual novels, adjusted by tag confidence threshold.
        return $builder->andWhere('tag_id IS NOT NULL AND votes >= avg * .5');
    }
}
