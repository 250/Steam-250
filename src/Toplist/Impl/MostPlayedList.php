<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class MostPlayedList extends Toplist
{
    public function __construct()
    {
        parent::__construct('most_played', 250);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->orderBy('players_2w', SortDirection::DESC);
    }
}
