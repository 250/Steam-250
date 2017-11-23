<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use ScriptFUSION\StaticClass;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Bottom100List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Linux250List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Mac250List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\ThisMonthList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\ThisQuarterList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\ThisYearList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Top250List;

final class ToplistAliases
{
    use StaticClass;

    private const ALIASES = [
        '250' => Top250List::class,
        'b100' => Bottom100List::class,
        '30d' => ThisMonthList::class,
        '90d' => ThisQuarterList::class,
        '365d' => ThisYearList::class,
        'mac' => Mac250List::class,
        'linux' => Linux250List::class,
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
