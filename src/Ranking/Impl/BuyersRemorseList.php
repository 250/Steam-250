<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class BuyersRemorseList extends Bottom100List
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 'buyers_remorse', 25);
    }
}
