<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Eloquent\Enumeration\AbstractEnumeration;

final class StaticPageName extends AbstractEnumeration
{
    public const ABOUT = 'ABOUT';
    public const AMBASSADORS = 'AMBASSADORS';
    public const TWEETS = 'TWEETS';
    public const SEARCH = 'SEARCH';
    public const HTTP_404 = 'HTTP_404';

    private static $classes = [
        self::ABOUT => AboutPage::class,
        self::AMBASSADORS => AmbassadorsPage::class,
        self::TWEETS => TweetsPage::class,
        self::SEARCH => SearchPage::class,
        self::HTTP_404 => Http404Page::class,
    ];

    private static $aliases = [
        self::ABOUT => 'about',
        self::AMBASSADORS => 'amb',
        self::TWEETS => 'tweets',
        self::SEARCH => 'search',
        self::HTTP_404 => '404',
    ];

    public function getAlias(): string
    {
        return self::$aliases["$this"];
    }

    public function getClassName(): string
    {
        return self::$classes["$this"];
    }
}
