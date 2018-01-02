<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

class HiddenGemsList extends Toplist
{
    public function __construct(string $id = 'hidden_gems')
    {
        parent::__construct($id, 250, Algorithm::HIDDEN_GEMS(), round(10 ** 6.42));
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        // Exclude visual novels.
        $builder->andWhere('tag IS NULL');
    }
}
