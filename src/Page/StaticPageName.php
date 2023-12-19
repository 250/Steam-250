<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Eloquent\Enumeration\AbstractEnumeration;

final class StaticPageName extends AbstractEnumeration
{
    public const PREVIEWS = 'PREVIEWS';
    public const ABOUT = 'ABOUT';
    public const PRIVACY = 'PRIVACY';
    public const AMBASSADORS = 'AMBASSADORS';
    public const CONTRIBUTORS = 'CONTRIBUTORS';
    public const TWEETS = 'TWEETS';
    public const SEARCH = 'SEARCH';
    public const HTTP_404 = 'HTTP_404';
    public const LOGIN = 'LOGIN';
    public const LOGOUT = 'LOGOUT';
    public const SYNC_GAMES = 'SYNC_GAMES';

    private static array $classes = [
        self::PREVIEWS => PreviewsPage::class,
        self::ABOUT => AboutPage::class,
        self::PRIVACY => PrivacyPage::class,
        self::AMBASSADORS => AmbassadorsPage::class,
        self::CONTRIBUTORS => ContributorsPage::class,
        self::TWEETS => TweetsPage::class,
        self::SEARCH => SearchPage::class,
        self::HTTP_404 => Http404Page::class,
        self::LOGIN => LoginPage::class,
        self::LOGOUT => LogoutPage::class,
        self::SYNC_GAMES => SyncGamesPage::class,
    ];

    private static array $aliases = [
        self::PREVIEWS => 'previews',
        self::ABOUT => 'about',
        self::PRIVACY => 'privacy',
        self::AMBASSADORS => 'amb',
        self::CONTRIBUTORS => 'contrib',
        self::TWEETS => 'tweets',
        self::SEARCH => 'search',
        self::HTTP_404 => '404',
        self::LOGIN => 'login',
        self::LOGOUT => 'logout',
        self::SYNC_GAMES => 'sync-games',
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
