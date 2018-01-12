<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Monolog\Logger;
use ScriptFUSION\Steam250\SiteGenerator\Database\DatabaseFactory;
use ScriptFUSION\Steam250\SiteGenerator\Rank\RankerFactory;

final class PageGeneratorFactory
{
    public function create(string $dbPath, string $extension): PageGenerator
    {
        return new PageGenerator(
            (new TwigFactory)->create($extension),
            $db = (new DatabaseFactory)->create($dbPath),
            (new RankerFactory)->create($db),
            new Logger('Generate'),
            (new MinifierFactory)->create()
        );
    }
}
