<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Toplist\ToplistAliases;

final class SiteGenerator
{
    private $generator;

    public function __construct(PageGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function generate(string $outPath): bool
    {
        foreach (ToplistAliases::getListClassNames() as $toplistClass) {
            $toplist = new $toplistClass;

            if (!$this->generator->generate($toplist, $outPath)) {
                return false;
            }
        }

        return true;
    }
}
