<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\Tag;

class TagList extends Top250List
{
    private $tag;

    public function __construct(string $tag)
    {
        $tagId = Tag::convertTagToId($tag);
        parent::__construct("tag/$tagId", 150);

        $this->tag = $tag;

        $this->setTemplate('tag');
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
