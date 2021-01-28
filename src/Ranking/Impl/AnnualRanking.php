<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class AnnualRanking extends RollingYearRanking
{
    public const EARLIEST_YEAR = 2006;

    private int $year;

    public function __construct(RankingDependencies $dependencies, int $year)
    {
        parent::__construct($dependencies);

        $this->year = $year;

        $this->setId("$year");
        $this->setTemplate('annual');
        $this->setTitle("Best of $year");
        $this->setDescription(
            "Top {$this->getLimit()} best Steam games released in $year, according to gamer reviews."
        );
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $yearStart = new \DateTimeImmutable("$this->year-1-1");
        $yearEnd = $yearStart->modify('1 year');

        $builder
            ->andWhere("app.release_date >= {$yearStart->getTimestamp()}")
            ->andWhere("app.release_date < {$yearEnd->getTimestamp()}")
        ;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
