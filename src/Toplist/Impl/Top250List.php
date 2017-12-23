<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class Top250List extends Toplist
{
    public function __construct($id = 'index', $limit = 250)
    {
        parent::__construct($id, $limit, Algorithm::LAPLACE_LOG(), .7);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Intentionally empty.
    }
}
