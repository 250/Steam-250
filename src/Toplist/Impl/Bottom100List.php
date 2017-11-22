<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class Bottom100List extends Toplist
{
    public function __construct()
    {
        parent::__construct('bottom100', Algorithm::BAYESIAN(), 500, 100, SortDirection::ASC());
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Intentionally empty.
    }
}
