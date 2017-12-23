<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use ScriptFUSION\StaticClass;

final class ToplistViolator
{
    use StaticClass;

    public static function violate(Toplist $toplist, ?Algorithm $algorithm, float $weight = null)
    {
        \Closure::bind(
            function () use ($algorithm, $weight): void {
                $algorithm && $this->algorithm = $algorithm;
                $weight && $this->weight = $weight;
            },
            $toplist,
            Toplist::class
        )();
    }
}
