<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use ScriptFUSION\Steam250\SiteGenerator\Page\Page;

trait AllowEmptyRanking
{
    public function export(): array
    {
        try {
            return parent::export();
        } catch (EmptyRankingException) {
            return Page::export();
        }
    }
}
