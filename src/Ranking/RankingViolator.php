<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use ScriptFUSION\StaticClass;

final class RankingViolator
{
    use StaticClass;

    public static function violate(Ranking $ranking, ?Algorithm $algorithm, float $weight = null)
    {
        \Closure::bind(
            function () use ($algorithm, $weight): void {
                $algorithm && $this->algorithm = $algorithm;
                $weight && $this->weight = $weight;
            },
            $ranking,
            Ranking::class
        )();
    }
}
