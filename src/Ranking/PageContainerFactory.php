<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticPageName;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\AnnualList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OwnersRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TagList;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\Tag;

final class PageContainerFactory
{
    public function create(Container $parent): Container
    {
        $container = new Container($parent);

        /** @var StaticPageName $name */
        foreach (StaticPageName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        /** @var RankingName $name */
        foreach (RankingName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        foreach (range(AnnualList::EARLIEST_YEAR, date('Y')) as $year) {
            $container->set($year, function () use ($year, $parent): Ranking {
                return new AnnualList($parent->get(RankingDependencies::class), $year);
            });

            $container->set("owners/$year", function () use ($year, $parent): Ranking {
                return new OwnersRanking($parent->get(RankingDependencies::class), $year);
            });
        }

        foreach (Queries::fetchPopularTags($container->get('db')) as $tag) {
            $container->set(Tag::convertTagToId($tag), function () use ($tag, $parent): Ranking {
                return new TagList($parent->get(RankingDependencies::class), $tag);
            });
        }

        return $container;
    }
}
