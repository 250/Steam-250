<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class AnnualRanking extends ThisYearRanking
{
    public const EARLIEST_YEAR = 2006;

    private $year;

    public function __construct(RankingDependencies $dependencies, int $year)
    {
        parent::__construct($dependencies);

        $this->year = $year;

        $this->setId("$year");
        $this->setTemplate('annual');
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $yearStart = new \DateTimeImmutable("$this->year-1-1");
        $yearEnd = $yearStart->modify('1 year');

        $builder
            ->andWhere("release_date >= {$yearStart->getTimestamp()}")
            ->andWhere("release_date < {$yearEnd->getTimestamp()}")
        ;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
