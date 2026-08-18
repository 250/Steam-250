<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class OldRanking extends DefaultRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 100);

        $this->setTemplate('annual');
    }

    public static function getStaticId(): string
    {
        return 'old';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return $builder->andWhere('app.release_date < ' . strtotime(AnnualRanking::EARLIEST_YEAR . '-1-1'));
    }
}
