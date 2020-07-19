<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Page\HomePage;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticPageName;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\AnnualRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\EarlyAccessRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OwnersRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TagRanking;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\Tag;

final class PageContainerFactory
{
    public function create(Container $parent): Container
    {
        $container = new Container($parent);
        $counter = 0;

        /** @var StaticPageName $name */
        foreach (StaticPageName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        /** @var RankingName $name */
        foreach (RankingName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
            ++$counter;
        }

        foreach (range(AnnualRanking::EARLIEST_YEAR, date('Y')) as $year) {
            $container->set($year, static function () use ($year, $parent): Ranking {
                return new AnnualRanking($parent->get(RankingDependencies::class), $year);
            });
            ++$counter;

            // Owners data is no longer current, so only show historical rankings.
            if ($year <= 2017) {
                $container->set("owners/$year", static function () use ($year, $parent): Ranking {
                    return new OwnersRanking($parent->get(RankingDependencies::class), $year);
                });
                ++$counter;
            }
        }

        // Tags.
        foreach (Queries::fetchPopularTags($container->get('db')) as $tag) {
            $container->set(Tag::convertTagToId($tag), static function () use ($tag, $parent): Ranking {
                return new TagRanking($parent->get(RankingDependencies::class), $tag);
            });
            ++$counter;
        }

        // Replace Early Access tag using special-cased implementation.
        $container->set(Tag::convertTagToId(EarlyAccessRanking::TAG), static function () use ($parent): Ranking {
            return new EarlyAccessRanking($parent->get(RankingDependencies::class));
        });

        $container->set(
            'home',
            fn () => new HomePage(
                $parent->get('db'),
                array_map(fn ($name) => $container->buildObject($name), HomePage::getRankings()),
                $counter
            )
        );

        return $container;
    }
}
