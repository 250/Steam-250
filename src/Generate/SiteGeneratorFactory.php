<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

final class SiteGeneratorFactory
{
    public function create(string $dbPath, bool $minify): SiteGenerator
    {
        $generator = (new PageGeneratorFactory)->create($dbPath);
        $generator->setMinify($minify);

        return new SiteGenerator($generator);
    }
}
