<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class ReviewsFullRanking extends DefaultRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies);

        $this->setTemplate('reviews_full');
        $this->setAlgorithm(null);
    }

    public static function getStaticId(): string
    {
        return 'reviews';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        return $builder
            ->orderBy('total_reviews', SortDirection::DESC)
        ;
    }
}
