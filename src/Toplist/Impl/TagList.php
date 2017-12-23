<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Tag;

class TagList extends Top250List
{
    private $tag;

    public function __construct(string $tag)
    {
        $tagId = Tag::convertTagToId($tag);
        parent::__construct("tag/$tagId", 100);

        $this->tag = $tag;

        $this->setTemplate('tag');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->join('app', 'app_tag', 'app_tag', 'id = app_id')
            ->andWhere('tag = :tag')
            ->setParameter('tag', $this->tag)
        ;
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
