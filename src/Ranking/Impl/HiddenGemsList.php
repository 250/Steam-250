<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class HiddenGemsList extends Ranking
{
    public function __construct(RankingDependencies $dependencies, string $id = 'hidden_gems')
    {
        parent::__construct($dependencies, $id, 250, Algorithm::HIDDEN_GEMS(), round(10 ** 6.42));
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Exclude visual novels, adjusted by tag confidence threshold.
        $builder->andWhere('tag IS NULL OR votes < avg * .5');
    }
}
