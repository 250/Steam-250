<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\SiteGenerator\Container\EnumerableContainer;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Tag;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\AnnualList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\TagList;

final class ToplistFactory
{
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function create(): EnumerableContainer
    {
        $container = new EnumerableContainer;

        foreach (ToplistName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        foreach (range(AnnualList::EARLIEST_YEAR, date('Y')) as $year) {
            $container->set($year, function () use ($year): Toplist {
                return new AnnualList($year);
            });
        }

        foreach (Queries::fetchPopularTags($this->database) as $tag) {
            $container->set(Tag::convertTagToId($tag), function () use ($tag): Toplist {
                return new TagList($tag);
            });
        }

        return $container;
    }
}
