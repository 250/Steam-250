<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class SteamDeckVerifiedRanking extends DefaultRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies);
    }

    public static function getStaticId(): string
    {
        return 'steam_deck_verified';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return $builder->andWhere('app.steam_deck = 3');
    }
}
