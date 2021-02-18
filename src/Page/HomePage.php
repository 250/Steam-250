<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DiscountRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenGemsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\MostPlayedRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingWeekRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Top250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TrendRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdUnder5List;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

class HomePage extends Page implements PreviousDatabaseAware
{
    use PreviousDatabase;

    private Connection $database;

    /** @var Ranking[] */
    private array $rankings;

    private int $rankingCount;

    private const RELATED_MAP = [
        'tag/sexual_content' => ['nudity'],
        'tag/co-op' => ['singleplayer', 'multiplayer', 'local co-op', 'online co-op'],
        'tag/pixel_graphics' => ['2d'],
        'tag/roguelike' => ['roguelite'],
    ];

    public function __construct(Connection $database, array $rankings, int $rankingCount)
    {
        parent::__construct($database, 'index');

        $this->database = $database;
        $this->rankings = $rankings;
        $this->rankingCount = $rankingCount;

        $this->setTemplate('home');
    }

    public function export(): array
    {
        $rankings = array_combine(
            array_map(fn ($r) => $r->getId(), $this->rankings),
            array_map(
                fn (Ranking $ranking) =>
                    [
                        'apps' => Queries::fetchRankedList($this->database, $ranking, $this->prevDb, 10),
                        'related' => self::RELATED_MAP[$ranking->getId()] ?? [],
                    ] + compact('ranking'),
                $this->rankings
            )
        );

        return parent::export() + compact('rankings') +
            [
                'total_games' => Queries::countGames($this->database),
                'total_rankings' => $this->rankingCount,
            ]
        ;
    }

    public static function getRankings(): array
    {
        return [
            Top250Ranking::class,
            HiddenGemsRanking::class,
            date('Y'),
            MostPlayedRanking::class,
            RollingWeekRanking::class,
            TrendRanking::class,
            UsdUnder5List::class,
            DiscountRanking::class,
            'free_to_play',
            'sexual_content',
            'co-op',
            'rpg',
            'pixel_graphics',
            'roguelike',
            'rts',
            'story_rich',
            'sandbox',
            'simulation',
        ];
    }
}
