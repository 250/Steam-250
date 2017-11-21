<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Monolog\Logger;
use ScriptFUSION\Steam250\SiteGenerator\Database\DatabaseFactory;
use ScriptFUSION\Steam250\SiteGenerator\Rank\RankerFactory;
use ScriptFUSION\Steam250\SiteGenerator\TwigFactory;

final class PageGeneratorFactory
{
    public function create(string $dbPath): PageGenerator
    {
        return new PageGenerator(
            (new TwigFactory)->create(),
            (new DatabaseFactory)->create($dbPath),
            (new RankerFactory)->create($dbPath),
            new Logger('Generate')
        );
    }
}
