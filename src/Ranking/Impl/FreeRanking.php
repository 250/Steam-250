<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class FreeRanking extends DefaultRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies);

        $this->setTitle('Free Games');
        $this->setDescription('Top 250 best free of charge games on Steam, according to gamer reviews.');
    }

    public static function getStaticId(): string
    {
        return 'price/free';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return $builder->andWhere('app.free = 1');
    }
}
