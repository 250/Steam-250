<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

interface PrecomputedRankingTable
{
    public function getSourceTable(): string;
}
