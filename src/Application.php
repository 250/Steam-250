<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use ScriptFUSION\Steam250\SiteGenerator\Generate\PageCommand;
use ScriptFUSION\Steam250\SiteGenerator\Generate\SiteCommand;

final class Application
{
    private $cli;

    public function __construct()
    {
        $this->cli = $cli = new \Symfony\Component\Console\Application;

        $cli->addCommands([
            new SiteCommand,
            new PageCommand,
        ]);
    }

    public function start(): int
    {
        return $this->cli->run();
    }

    public static function getAppPath(string $path): string
    {
        return __DIR__ . "/../$path";
    }
}
