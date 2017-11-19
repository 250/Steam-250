<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\DriverManager;
use Monolog\Logger;
use ScriptFUSION\Top250\Shared\Algorithm;

final class SiteGeneratorFactory
{
    public function create(string $dbPath, string $outPath, Algorithm $algorithm, float $weight): SiteGenerator
    {
        return new SiteGenerator(
            (new TwigFactory)->create(),
            DriverManager::getConnection(['url' => "sqlite:///$dbPath"]),
            new Logger('Generate'),
            $outPath,
            $algorithm,
            $weight
        );
    }
}
