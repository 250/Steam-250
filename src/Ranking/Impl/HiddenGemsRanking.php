<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class HiddenGemsRanking extends Ranking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 250, Algorithm::HIDDEN_GEMS(), 45000.);

        $this->setTitle('Hidden Gems');
        $this->setDescription('Top 250 highly rated Steam games that few know but many love.');
    }

    public static function getStaticId(): string
    {
        return 'hidden_gems';
    }

    public function customizeQuery(QueryBuilder $builder): ?QueryBuilder
    {
        // Exclude visual novels, adjusted by tag confidence threshold.
        return $builder->andWhere('tag_id IS NULL OR votes < avg * .5');
    }
}
