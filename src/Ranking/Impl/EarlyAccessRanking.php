<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use Doctrine\DBAL\Query\QueryBuilder;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

/**
 * Defines a ranking for early access games using the dedicated "ea" field instead of relying on tags.
 *
 * Due to a long-standing bug that appeared in the Steam store, games that have left early access may continue to be
 * tagged as such, and thus tags are no longer a reliable indicator of whether a game is currently in early access.
 */
class EarlyAccessRanking extends TagRanking
{
    public const TAG = 'Early Access';

    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, self::TAG);
    }

    public function customizeQuery(QueryBuilder $builder): void
    {
        $builder->andWhere('ea = 1');
    }
}
