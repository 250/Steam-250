<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

class BuyersRemorseList extends Bottom100List
{
    public function __construct()
    {
        parent::__construct('buyers_remorse', 25);
    }
}
