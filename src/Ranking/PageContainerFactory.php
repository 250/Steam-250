<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Page\HomePage;
use ScriptFUSION\Steam250\SiteGenerator\Page\StaticPageName;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\AnnualRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\EarlyAccessRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ReviewsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TagRanking;
use ScriptFUSION\Steam250\SiteGenerator\Tag\Tag;

final class PageContainerFactory
{
    public function create(Container $parent): Container
    {
        $container = new Container($parent);
        $counter = 0;

        foreach (StaticPageName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        foreach (RankingName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
            ++$counter;
        }

        foreach (range(AnnualRanking::EARLIEST_YEAR, date('Y')) as $year) {
            $ranking = new AnnualRanking($dependencies = $parent->get(RankingDependencies::class), $year);
            $container->set($ranking->getId(), $ranking);
            ++$counter;

            $ranking = new ReviewsRanking($dependencies, $year);
            $container->set($ranking->getId(), $ranking);
            ++$counter;
        }

        // Tags.
        $tags = Queries::fetchPopularTags($container->get('db'));
        usort($tags, static fn ($a, $b) => $a['name'] <=> $b['name']);
        foreach ($tags as $tag) {
            $container->set(
                Tag::convertTagToId($tag['name']),
                fn () => new TagRanking($parent->get(RankingDependencies::class), $tag['name'], $tag['id'])
            );
            ++$counter;
        }

        // Replace Early Access tag using special-cased implementation.
        $container->set(
            Tag::convertTagToId(EarlyAccessRanking::TAG),
            fn () => new EarlyAccessRanking($parent->get(RankingDependencies::class))
        );

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
