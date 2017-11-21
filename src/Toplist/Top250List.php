<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

final class Top250List extends Toplist
{
    public function __construct()
    {
        parent::__construct('index', Algorithm::WILSON(), 4.4172, 250);
    }
}
