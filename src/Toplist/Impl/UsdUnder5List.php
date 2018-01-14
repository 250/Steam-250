<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

class UsdUnder5List extends PriceRangeList
{
    public function __construct()
    {
        parent::__construct('price/under5', 0, 500);
    }
}
