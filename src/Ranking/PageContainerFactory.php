<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Doctrine\DBAL\Connection;
use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\Tag;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\AnnualList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TagList;

final class PageContainerFactory
{
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function create(): Container
    {
        $container = new Container;

        /** @var RankingName $name */
        foreach (RankingName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        foreach (range(AnnualList::EARLIEST_YEAR, date('Y')) as $year) {
            $container->set($year, function () use ($year): Ranking {
                return new AnnualList($year);
            });
        }

        foreach (Queries::fetchPopularTags($this->database) as $tag) {
            $container->set(Tag::convertTagToId($tag), function () use ($tag): Ranking {
                return new TagList($tag);
            });
        }

        return $container;
    }
}
