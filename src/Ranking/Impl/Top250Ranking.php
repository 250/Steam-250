<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class Top250Ranking extends DefaultRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies, $limit = 250)
    {
        parent::__construct($dependencies, $limit);

        $this->setTitle('Steam Top 250');
        $this->setDescription('Top 250 best Steam games of all time according to gamer reviews.');
    }

    public static function getStaticId(): string
    {
        return 'top250';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return null;
    }
}
