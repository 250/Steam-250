<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

final class CustomToplist extends Toplist
{
    private $toplist;

    public function __construct(Toplist $parent, Algorithm $algorithm = null, float $weight = null)
    {
        parent::__construct(
            $parent->getTemplate(),
            $algorithm ?: $parent->getAlgorithm(),
            $weight ?: $parent->getWeight(),
            $parent->getLimit(),
            $parent->getDirection()
        );

        $this->toplist = $parent;
    }

    public function getParentToplist(): Toplist
    {
        return $this->toplist;
    }
}
