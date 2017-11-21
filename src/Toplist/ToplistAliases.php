<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use ScriptFUSION\StaticClass;

final class ToplistAliases
{
    use StaticClass;

    private const ALIASES = [
        '250' => Top250List::class,
        'top250' => Top250List::class,
        'bottom' => Bottom100List::class,
        'b100' => Bottom100List::class,
    ];

    public static function createToplist(string $name): Toplist
    {
        $name = self::ALIASES[$name];

        return new $name;
    }

    public static function getListClassNames(): array
    {
        return array_unique(self::ALIASES);
    }
}
