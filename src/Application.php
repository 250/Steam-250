<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

final class Application
{
    public static function getAppPath(string $path): string
    {
        return __DIR__ . "/../$path";
    }
}
