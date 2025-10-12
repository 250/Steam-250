<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Eloquent\Enumeration\AbstractEnumeration;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\AdultRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Bottom100Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\BuyersRemorseRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\CollageRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DeveloperRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DiscountRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DlcRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\FreeRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenGemsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenNovelsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Linux250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Mac250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\MostPlayedRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\OldRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\PublisherRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ReviewsFullRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\ReviewsOldRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingMonthRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingQuarterRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingWeekRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingYearRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\SteamDeckPlayableRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\SteamDeckVerifiedRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Top250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd10To15Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd15To20Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Usd5To10Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdOver20Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdUnder5Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\VrTop250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\VrxRanking;

final class RankingName extends AbstractEnumeration
{
    public const TOP_250 = 'TOP_250';
    public const GEMS = 'GEMS';
    public const BOTTOM_100 = 'BOTTOM_100';
    public const DLC = 'DLC';
    public const DISCOUNT = 'DISCOUNT';
    public const FREE = 'FREE';
    public const USD_UNDER_5 = 'USD_UNDER_5';
    public const USD5_10 = 'USD5_10';
    public const USD10_15 = 'USD10_15';
    public const USD15_20 = 'USD15_20';
    public const USD_OVER_20 = 'USD_OVER_20';
    public const D_7 = 'D_7';
    public const D_30 = 'D_30';
    public const D_90 = 'D_90';
    public const D_365 = 'D_365';
    public const OLD = 'OLD';
    public const REVIEW = 'REVIEW';
    public const OLD_REVIEW = 'OLD_REVIEW';
    public const MAC = 'MAC';
    public const LINUX = 'LINUX';
    public const VR = 'VR';
    public const VRX = 'VRX';
    public const STEAM_DECK = 'STEAM_DECK';
    public const STEAM_DECK_PLAYABLE = 'STEAM_DECK_PLAYABLE';
    public const HVN = 'HVN';
    public const REMORSE = 'REMORSE';
    public const DEVELOPER = 'DEVELOPER';
    public const PUBLISHER = 'PUBLISHER';
    public const PLAYERS = 'PLAYERS';
    public const ADULT = 'ADULT';
    public const COLLAGE = 'COLLAGE';

    private static array $classes = [
        self::TOP_250 => Top250Ranking::class,
        self::GEMS => HiddenGemsRanking::class,
        self::BOTTOM_100 => Bottom100Ranking::class,
        self::DLC => DlcRanking::class,
        self::DISCOUNT => DiscountRanking::class,
        self::FREE => FreeRanking::class,
        self::USD_UNDER_5 => UsdUnder5Ranking::class,
        self::USD5_10 => Usd5To10Ranking::class,
        self::USD10_15 => Usd10To15Ranking::class,
        self::USD15_20 => Usd15To20Ranking::class,
        self::USD_OVER_20 => UsdOver20Ranking::class,
        self::D_7 => RollingWeekRanking::class,
        self::D_30 => RollingMonthRanking::class,
        self::D_90 => RollingQuarterRanking::class,
        self::D_365 => RollingYearRanking::class,
        self::OLD => OldRanking::class,
        self::REVIEW => ReviewsFullRanking::class,
        self::OLD_REVIEW => ReviewsOldRanking::class,
        self::MAC => Mac250Ranking::class,
        self::LINUX => Linux250Ranking::class,
        self::VR => VrTop250Ranking::class,
        self::VRX => VrxRanking::class,
        self::STEAM_DECK => SteamDeckVerifiedRanking::class,
        self::STEAM_DECK_PLAYABLE => SteamDeckPlayableRanking::class,
        self::HVN => HiddenNovelsRanking::class,
        self::REMORSE => BuyersRemorseRanking::class,
        self::DEVELOPER => DeveloperRanking::class,
        self::PUBLISHER => PublisherRanking::class,
        self::PLAYERS => MostPlayedRanking::class,
        self::ADULT => AdultRanking::class,
        self::COLLAGE => CollageRanking::class,
    ];

    private static array $aliases = [
        self::TOP_250 => '250',
        self::GEMS => 'gems',
        self::BOTTOM_100 => 'b100',
        self::DLC => 'dlc',
        self::DISCOUNT => 'discount',
        self::FREE => 'free',
        self::USD_UNDER_5 => 'u5',
        self::USD5_10 => '5-10',
        self::USD10_15 => '10-15',
        self::USD15_20 => '15-20',
        self::USD_OVER_20 => 'o20',
        self::D_7 => '7d',
        self::D_30 => '30d',
        self::D_90 => '90d',
        self::D_365 => '365d',
        self::OLD => 'old',
        self::REVIEW => 'review',
        self::OLD_REVIEW => 'rold',
        self::MAC => 'mac',
        self::LINUX => 'linux',
        self::VR => 'vr',
        self::VRX => 'vrx',
        self::STEAM_DECK => 'deck',
        self::STEAM_DECK_PLAYABLE => 'deckp',
        self::HVN => 'hvn',
        self::REMORSE => 'remorse',
        self::DEVELOPER => 'dev',
        self::PUBLISHER => 'pub',
        self::PLAYERS => 'players',
        self::ADULT => '18',
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
