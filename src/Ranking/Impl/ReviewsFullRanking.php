<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ReviewsFullRanking extends DefaultRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'reviews');

        $this->setTemplate('reviews_full');
        $this->setAlgorithm(null);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->orderBy('total_reviews', SortDirection::DESC)
        ;
    }
}
