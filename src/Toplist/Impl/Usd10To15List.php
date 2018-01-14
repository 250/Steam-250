<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

class Usd10To15List extends PriceRangeList
{
    public function __construct()
    {
        parent::__construct('price/10-15', 1000, 1500);
    }
}
