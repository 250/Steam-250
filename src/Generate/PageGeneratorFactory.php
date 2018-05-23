<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\Shared\Log\LoggerFactory;

final class PageGeneratorFactory
{
    public function create(string $extension): PageGenerator
    {
        return new PageGenerator(
            (new TwigFactory)->create($extension),
            (new LoggerFactory)->create('Generator', false),
            (new MinifierFactory)->create()
        );
    }
}
