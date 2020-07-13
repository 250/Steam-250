<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenGemsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingWeekRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Top250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TrendRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

class HomePage extends Page implements PreviousDatabaseAware
{
    use PreviousDatabase;

    private Connection $database;

    /** @var Ranking[] */
    private array $rankings;

    public function __construct(Connection $database, array $rankings)
    {
        parent::__construct($database, 'index');

        $this->database = $database;
        $this->rankings = $rankings;
    }

    public function export(): array
    {
        $rankings = array_combine(
            array_map(fn (Ranking $r) => $r->getId(), $this->rankings),
            array_map(
                fn (Ranking $r) =>
                    Queries::fetchRankedList($this->database, $r->setLimit(10), $this->prevDb),
                $this->rankings
            )
        );

        return parent::export() + compact('rankings');
    }

    public static function getRankings(): array
    {
        return [
            Top250Ranking::class,
            HiddenGemsRanking::class,
            date('Y'),
            RollingWeekRanking::class,
            TrendRanking::class,
        ];
    }
}
