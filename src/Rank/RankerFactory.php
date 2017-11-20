<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ScriptFUSION\Steam250\SiteGenerator\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Database\DatabaseFactory;

final class RankerFactory
{
    public function create(string $dbPath, Algorithm $algorithm, float $weight): Ranker
    {
        return new Ranker(
            (new DatabaseFactory)->create($dbPath),
            (new Logger('Decorate'))->pushHandler(new StreamHandler(STDERR, Logger::INFO)),
            $algorithm,
            $weight
        );
    }
}
