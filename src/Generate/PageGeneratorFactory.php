<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Monolog\Logger;

final class PageGeneratorFactory
{
    public function create(string $extension): PageGenerator
    {
        return new PageGenerator(
            (new TwigFactory)->create($extension),
            new Logger('Generator'),
            (new MinifierFactory)->create()
        );
    }
}
