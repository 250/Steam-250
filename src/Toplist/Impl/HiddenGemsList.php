<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class HiddenGemsList extends Toplist
{
    public function __construct()
    {
        parent::__construct('hidden_gems', Algorithm::HIDDEN_GEMS(), round(10 ** 6.42), 250);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Intentionally empty.
    }
}
