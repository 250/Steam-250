<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Eloquent\Enumeration\AbstractEnumeration;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Bottom100List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\BuyersRemorseList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Club250List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\CollageList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DiscountList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DlcList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenGemsList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenNovelsList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Linux250List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Mac250List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\MostPlayedList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OldList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OwnersFullRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OwnersOldRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ThisMonthList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ThisQuarterList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ThisWeekRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ThisYearList;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Top250List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd10To15List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd5To10List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdOver15List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdUnder5List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\VrTop250List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\VrxRanking;

final class RankingName extends AbstractEnumeration
{
    public const TOP_250 = 'TOP_250';
    public const GEMS = 'GEMS';
    public const BOTTOM_100 = 'BOTTOM_100';
    public const DLC = 'DLC';
    public const DISCOUNT = 'DISCOUNT';
    public const USD_UNDER_5 = 'USD_UNDER_5';
    public const USD5_10 = 'USD5_10';
    public const USD10_15 = 'USD10_15';
    public const USD_OVER_15 = 'USD_OVER_15';
    public const D_7 = 'D_7';
    public const D_30 = 'D_30';
    public const D_90 = 'D_90';
    public const D_365 = 'D_365';
    public const OLD = 'OLD';
    public const OWNERS = 'OWNERS';
    public const OLD_OWN = 'OLD_OWN';
    public const MAC = 'MAC';
    public const LINUX = 'LINUX';
    public const VR = 'VR';
    public const VRX = 'VRX';
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
        self::USD_UNDER_5 => UsdUnder5List::class,
        self::USD5_10 => Usd5To10List::class,
        self::USD10_15 => Usd10To15List::class,
        self::USD_OVER_15 => UsdOver15List::class,
        self::D_7 => ThisWeekRanking::class,
        self::D_30 => ThisMonthList::class,
        self::D_90 => ThisQuarterList::class,
        self::D_365 => ThisYearList::class,
        self::OLD => OldList::class,
        self::OWNERS => OwnersFullRanking::class,
        self::OLD_OWN => OwnersOldRanking::class,
        self::MAC => Mac250List::class,
        self::LINUX => Linux250List::class,
        self::VR => VrTop250List::class,
        self::VRX => VrxRanking::class,
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
        self::USD_UNDER_5 => 'u5',
        self::USD5_10 => '5-15',
        self::USD10_15 => '10-15',
        self::USD_OVER_15 => 'o15',
        self::D_7 => '7d',
        self::D_30 => '30d',
        self::D_90 => '90d',
        self::D_365 => '365d',
        self::OLD => 'old',
        self::OWNERS => 'owners',
        self::OLD_OWN => 'oold',
        self::MAC => 'mac',
        self::LINUX => 'linux',
        self::VR => 'vr',
        self::VRX => 'vrx',
        self::HVN => 'hvn',
        self::REMORSE => 'remorse',
        self::PLAYERS => 'players',
        self::CLUB_250 => 'club',
        self::COLLAGE => 'collage',
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
