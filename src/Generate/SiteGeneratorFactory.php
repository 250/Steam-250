<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

final class SiteGeneratorFactory
{
    public function create(string $dbPath): SiteGenerator
    {
        return new SiteGenerator((new PageGeneratorFactory)->create($dbPath));
    }
}
