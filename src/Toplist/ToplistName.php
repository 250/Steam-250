<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use Eloquent\Enumeration\AbstractEnumeration;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Bottom100List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\BuyersRemorseList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Club250List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\CollageList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\DiscountList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\DlcList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\HiddenGemsList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\HiddenNovelsList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Linux250List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Mac250List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\MostPlayedList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\ThisMonthList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\ThisQuarterList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\ThisYearList;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Top250List;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\VrTop250List;

final class ToplistName extends AbstractEnumeration
{
    public const TOP_250 = 'TOP_250';
    public const GEMS = 'GEMS';
    public const BOTTOM_100 = 'BOTTOM_100';
    public const DLC = 'DLC';
    public const DISCOUNT = 'DISCOUNT';
    public const D_30 = 'D_30';
    public const D_90 = 'D_90';
    public const D_365 = 'D_365';
    public const MAC = 'MAC';
    public const LINUX = 'LINUX';
    public const VR = 'VR';
    public const HVN = 'HVN';
    public const REMORSE = 'REMORSE';
    public const PLAYERS = 'PLAYERS';
    public const CLUB_250 = 'CLUB_250';
    public const COLLAGE = 'COLLAGE';

    private static $classes = [
        self::TOP_250 => Top250List::class,
        self::GEMS => HiddenGemsList::class,
        self::BOTTOM_100 => Bottom100List::class,
        self::DLC => DlcList::class,
        self::DISCOUNT => DiscountList::class,
        self::D_30 => ThisMonthList::class,
        self::D_90 => ThisQuarterList::class,
        self::D_365 => ThisYearList::class,
        self::MAC => Mac250List::class,
        self::LINUX => Linux250List::class,
        self::VR => VrTop250List::class,
        self::HVN => HiddenNovelsList::class,
        self::REMORSE => BuyersRemorseList::class,
        self::PLAYERS => MostPlayedList::class,
        self::CLUB_250 => Club250List::class,
        self::COLLAGE => CollageList::class,
    ];

    private static $aliases = [
        self::TOP_250 => '250',
        self::GEMS => 'gems',
        self::BOTTOM_100 => 'b100',
        self::DLC => 'dlc',
        self::DISCOUNT => 'discount',
        self::D_30 => '30d',
        self::D_90 => '90d',
        self::D_365 => '365d',
        self::MAC => 'mac',
        self::LINUX => 'linux',
        self::VR => 'vr',
        self::HVN => 'hvn',
        self::REMORSE => 'remorse',
        self::PLAYERS => 'players',
        self::CLUB_250 => 'club',
        self::COLLAGE => 'collage',
    ];

    public static function getClassNames(): array
    {
        return self::$classes;
    }

    public function getAlias(): string
    {
        return self::$aliases["$this"];
    }

    public function getClassName(): string
    {
        return self::$classes["$this"];
    }
}
