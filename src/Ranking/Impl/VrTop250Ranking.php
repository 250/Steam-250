<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\Shared\Platform;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\AllowEmptyRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class VrTop250Ranking extends DefaultRanking
{
    use AllowEmptyRanking;

    public function __construct(RankingDependencies $dependencies, $id = 'vr250')
    {
        parent::__construct($dependencies, $id);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('app.platforms & ' . (Platform::VIVE | Platform::OCULUS | Platform::WMR | Platform::INDEX));
    }
}
