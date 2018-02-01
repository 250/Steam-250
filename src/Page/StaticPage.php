<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Eloquent\Enumeration\AbstractEnumeration;

final class StaticPage extends AbstractEnumeration
{
    public const AMBASSADORS = 'AMBASSADORS';
    public const TWEETS = 'TWEETS';

    private static $classes = [
        self::AMBASSADORS => AmbassadorsPage::class,
        self::TWEETS => TweetsPage::class,
    ];

    private static $aliases = [
        self::AMBASSADORS => 'amb',
        self::TWEETS => 'tweets',
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
