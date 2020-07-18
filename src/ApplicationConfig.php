<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

final class ApplicationConfig
{
    private string $dbPath;

    public function __construct(string $dbPath)
    {
        $this->dbPath = $dbPath;
    }

    public function getDbPath(): string
    {
        return $this->dbPath;
    }
}
