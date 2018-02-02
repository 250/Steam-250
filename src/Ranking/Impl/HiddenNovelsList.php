<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class HiddenNovelsList extends HiddenGemsList
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'hidden_novels');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Include only visual novels, adjusted by tag confidence threshold.
        $builder->andWhere('tag IS NOT NULL AND votes >= avg * .5');
    }
}
