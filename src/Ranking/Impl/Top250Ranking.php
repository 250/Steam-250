<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

final class Top250Ranking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies, $id = 'top250', $limit = 250)
    {
        parent::__construct($dependencies, $id, $limit);

        $this->setTitle('Steam Top 250');
        $this->setDescription('Top 250 best Steam games of all time according to gamer reviews.');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Intentionally empty.
    }
}
