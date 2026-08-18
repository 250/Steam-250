<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl;

use ScriptFUSION\Steam250\SiteGenerator\Page\StaticId;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

class PublisherRanking extends DevlisherRanking implements StaticId
{
    public function __construct(RankingDependencies $dependencies)
    {
        parent::__construct($dependencies, 10);

        $this->setWeight(1);
    }

    public static function getStaticId(): string
    {
        return 'publisher';
    }
}
