<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;
use ScriptFUSION\Steam250\SiteGenerator\Tag\Tag;
use ScriptFUSION\Steam250\SiteGenerator\Tag\TagDirectoryStatePacker;

class TagRanking extends DefaultRanking
{
    private Connection $database;

    public function __construct(
        RankingDependencies $dependencies,
        private readonly string $tag,
        private readonly ?int $tagId = null
    ) {
        $tagNameId = Tag::convertTagToId($tag);
        parent::__construct($dependencies, "tag/$tagNameId", 150);

        $this->database = $dependencies->getDatabase();

        $this->setTemplate('tag');
        $this->setTitle($tag);
        $this->setDescription(
            "Top 150 best Steam games of all time tagged with <em>$tag</em>, according to gamer reviews."
        );
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder
            ->join('app', 'app_tag', 'app_tag', 'app.id = app_tag.app_id')
            ->join('app_tag', 'tag', 'tag', 'app_tag.tag_id = tag.id')
            ->join(
                'app',
                '(
                    SELECT app_id, AVG(votes) avg
                    FROM app_tag
                    GROUP BY app_id
                )',
                'avg',
                'app.id = avg.app_id'
            )
            ->andWhere('tag.name = :tag AND votes >= avg * .5')
            ->setParameter('tag', $this->tag)
        ;
    }

    public function export(): array
    {
        $cat = $this->fetchTagCategory();

        return parent::export() + [
            'tag_category' => $cat + ['hash' => TagDirectoryStatePacker::packSingleCategory($cat['id'])],
            'tag_categories' => $this->fetchTagCategories(),
        ];
    }

    private function fetchTagCategories(): array
    {
        return $this->database->fetchAllAssociative('
            SELECT c.*, (SELECT min(tag.name) FROM tag WHERE tag.category = c.short_name) first_tag
            FROM tag_cat c
            ORDER BY c.id
        ');
    }

    private function fetchTagCategory(): array
    {
        return $this->database->fetchAssociative(
            'SELECT tag_cat.* FROM tag_cat JOIN tag ON tag.category = tag_cat.short_name AND tag.name = ? LIMIT 1',
            [$this->tag]
        );
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getTagId(): ?int
    {
        return $this->tagId;
    }
}
