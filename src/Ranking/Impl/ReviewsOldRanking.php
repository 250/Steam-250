<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ReviewsOldRanking extends OldRanking
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies);

        $this->setId("reviews/old");
        $this->setTemplate('reviews');
        $this->setAlgorithm(null);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        parent::customizeQuery($builder);

        $builder
            ->orderBy('total_reviews', SortDirection::DESC)
        ;
    }
}
