<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class MostPlayedRanking extends Ranking implements CustomRankingFetch
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'most_played', 250);

        $this->setTitle('Most Played');
        $this->setDescription(
            'Top 250 most played Steam games, based on average number of concurrent players in the last seven days.'
        );
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->join('app', 'app_players', 'app_players', 'app.id = app_id')
            ->orderBy('average_players_7d', SortDirection::DESC)
        ;
    }

    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        $builder
            ->join('app', 'app_players', 'app_players', 'id = app_players.app_id')
            ->addSelect('app_players.average_players_7d')
        ;
    }
}
