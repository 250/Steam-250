<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\DriverManager;
use Monolog\Logger;

final class SiteGeneratorFactory
{
    public function create(string $dbPath, string $outPath): SiteGenerator
    {
        return new SiteGenerator(
            (new TwigFactory)->create(),
            DriverManager::getConnection(['url' => "sqlite:///$dbPath"]),
            new Logger('Generate'),
            $outPath
        );
    }
}
