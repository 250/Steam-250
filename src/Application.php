<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use ScriptFUSION\Steam250\SiteGenerator\Generate\PageCommand;

final class Application
{
    private $cli;

    public function __construct()
    {
        $this->cli = $cli = new \Symfony\Component\Console\Application;

        $cli->add(new PageCommand);
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
