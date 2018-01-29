<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Top250\Shared\Platform;

class Linux250List extends Top250List
{
    public function __construct()
    {
        parent::__construct('linux250');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        parent::customizeQuery($builder);

        $builder->andWhere('platforms & ' . Platform::LINUX);
    }
}
