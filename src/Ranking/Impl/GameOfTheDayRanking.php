<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;
use ScriptFUSION\Steam250\SiteGenerator\Rank\CustomRankingFetch;

class GameOfTheDayRanking extends Top250Ranking implements CustomRankingFetch
{
    public function customizeRankingFetch(QueryBuilder $builder): void
    {
        $randomizer = new Randomizer(new PcgOneseq128XslRr64((int)date('Ymd')));

        $builder
            ->andWhere('rank.rank = :rank')
                ->setParameter('rank', $randomizer->getInt(1, $this->getLimit()))
        ;
    }
}
