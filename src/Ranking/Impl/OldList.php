<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class OldList extends Top250List
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'old', 100);

        $this->setTemplate('annual');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('release_date < ' . strtotime(AnnualList::EARLIEST_YEAR . '-1-1'));
    }
}
