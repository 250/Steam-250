<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class RollingWeekRanking extends RollingRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, '-7 day', 'week', 7, 50);

        $this->setTitle("Week Top {$this->getLimit()}");
        $this->setDescription(
            "Top {$this->getLimit()} best Steam games released in the last 7 days, according to gamer reviews."
        );
    }

    public static function getStaticId(): string
    {
        return '7day';
    }
}
