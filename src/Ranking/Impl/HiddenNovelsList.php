<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;

class HiddenNovelsList extends HiddenGemsList
{
    public function __construct()
    {
        parent::__construct('hidden_novels');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Include only visual novels.
        $builder->andWhere('tag IS NOT NULL');
    }
}
