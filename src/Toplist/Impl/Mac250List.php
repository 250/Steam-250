<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Top250\Shared\Platform;

class Mac250List extends Top250List
{
    public function __construct()
    {
        parent::__construct('mac250');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        parent::customizeQuery($builder);

        $builder->andWhere('platforms & ' . Platform::MAC);
    }
}
