<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;

final class Bottom100List extends Toplist
{
    public function __construct()
    {
        parent::__construct('bottom100', Algorithm::BAYESIAN(), 500, 100, SortDirection::ASC());
    }
}
