<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\Tag;

class TagRanking extends DefaultRanking
{
    private string $tag;

    public function __construct(RankingDependencies $dependencies, string $tag)
    {
        $tagId = Tag::convertTagToId($tag);
        parent::__construct($dependencies, "tag/$tagId", 150);

        $this->tag = $tag;

        $this->setTemplate('tag');
        $this->setTitle($tag);
        $this->setDescription(
            "Top 150 best Steam games of all time tagged with <em>$tag</em>, according to gamer reviews."
        );
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->join('app', 'app_tag', 'app_tag', 'id = app_tag.app_id')
            ->join(
                'app',
                '(
                    SELECT app_id, AVG(votes) as avg
                    FROM app_tag
                    GROUP BY app_id
                )',
                'avg',
                'id = avg.app_id'
            )
            ->andWhere('tag = :tag AND votes >= avg * .5')
            ->setParameter('tag', $this->tag)
        ;
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
