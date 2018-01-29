<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

class Usd5To10List extends PriceRangeList
{
    public function __construct()
    {
        parent::__construct('price/5-10', 500, 1000);
    }
}
