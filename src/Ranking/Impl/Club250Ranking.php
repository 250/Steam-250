<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

abstract class Club250Ranking extends Ranking
{
    abstract public function getUrl(): string;

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return null;
    }
}
