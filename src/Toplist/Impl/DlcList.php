<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl;

use Doctrine\DBAL\Query\QueryBuilder;

class DlcList extends Top250List
{
    public function __construct()
    {
        parent::__construct('dlc');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->where('type = \'dlc\'');
    }
}
