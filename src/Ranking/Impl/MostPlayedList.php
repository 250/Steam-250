<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

class MostPlayedList extends Ranking
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
