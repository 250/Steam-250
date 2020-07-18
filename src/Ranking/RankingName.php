<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Eloquent\Enumeration\AbstractEnumeration;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Bottom100Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\BuyersRemorseRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Club250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\CollageRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DeveloperRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DiscountRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DlcRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenGemsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenNovelsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Linux250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Mac250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\MostPlayedRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OldRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OwnersOldRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\PublisherRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingMonthRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingQuarterRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingWeekRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingYearRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Top250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TrendRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd10To15List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd5To10List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdOver15Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdUnder5List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\VrTop250Ranking;
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
    public const OLD_OWN = 'OLD_OWN';
    public const MAC = 'MAC';
    public const LINUX = 'LINUX';
    public const VR = 'VR';
    public const VRX = 'VRX';
    public const HVN = 'HVN';
    public const REMORSE = 'REMORSE';
    public const DEVELOPER = 'DEVELOPER';
    public const PUBLISHER = 'PUBLISHER';
    public const PLAYERS = 'PLAYERS';
    public const TREND = 'TREND';
    public const CLUB_250 = 'CLUB_250';
    public const COLLAGE = 'COLLAGE';

    private static array $classes = [
        self::TOP_250 => Top250Ranking::class,
        self::GEMS => HiddenGemsRanking::class,
        self::BOTTOM_100 => Bottom100Ranking::class,
        self::DLC => DlcRanking::class,
        self::DISCOUNT => DiscountRanking::class,
        self::USD_UNDER_5 => UsdUnder5List::class,
        self::USD5_10 => Usd5To10List::class,
        self::USD10_15 => Usd10To15List::class,
        self::USD_OVER_15 => UsdOver15Ranking::class,
        self::D_7 => RollingWeekRanking::class,
        self::D_30 => RollingMonthRanking::class,
        self::D_90 => RollingQuarterRanking::class,
        self::D_365 => RollingYearRanking::class,
        self::OLD => OldRanking::class,
        self::OLD_OWN => OwnersOldRanking::class,
        self::MAC => Mac250Ranking::class,
        self::LINUX => Linux250Ranking::class,
        self::VR => VrTop250Ranking::class,
        self::VRX => VrxRanking::class,
        self::HVN => HiddenNovelsRanking::class,
        self::REMORSE => BuyersRemorseRanking::class,
        self::DEVELOPER => DeveloperRanking::class,
        self::PUBLISHER => PublisherRanking::class,
        self::PLAYERS => MostPlayedRanking::class,
        self::TREND => TrendRanking::class,
        self::CLUB_250 => Club250Ranking::class,
        self::COLLAGE => CollageRanking::class,
    ];

    private static array $aliases = [
        self::TOP_250 => '250',
        self::GEMS => 'gems',
        self::BOTTOM_100 => 'b100',
        self::DLC => 'dlc',
        self::DISCOUNT => 'discount',
        self::USD_UNDER_5 => 'u5',
        self::USD5_10 => '5-10',
        self::USD10_15 => '10-15',
        self::USD_OVER_15 => 'o15',
        self::D_7 => '7d',
        self::D_30 => '30d',
        self::D_90 => '90d',
        self::D_365 => '365d',
        self::OLD => 'old',
        self::OLD_OWN => 'oold',
        self::MAC => 'mac',
        self::LINUX => 'linux',
        self::VR => 'vr',
        self::VRX => 'vrx',
        self::HVN => 'hvn',
        self::REMORSE => 'remorse',
        self::DEVELOPER => 'dev',
        self::PUBLISHER => 'pub',
        self::PLAYERS => 'players',
        self::TREND => 'trend',
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
