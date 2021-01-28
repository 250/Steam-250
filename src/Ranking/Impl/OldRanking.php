<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class OldRanking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'old', 100);

        $this->setTemplate('annual');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('app.release_date < ' . strtotime(AnnualRanking::EARLIEST_YEAR . '-1-1'));
    }
}
