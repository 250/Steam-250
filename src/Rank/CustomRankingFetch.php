<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Query\QueryBuilder;

interface CustomRankingFetch
{
    public function customizeRankingFetch(QueryBuilder $builder): void;
}
