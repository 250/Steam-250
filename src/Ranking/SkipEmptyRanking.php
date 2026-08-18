<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

trait SkipEmptyRanking
{
    public function export(): array
    {
        try {
            return parent::export();
        } catch (EmptyRankingException) {
            throw new SkipEmptyRankingException();
        }
    }
}
