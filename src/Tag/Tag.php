<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Tag;

use ScriptFUSION\StaticClass;

final class Tag
{
    use StaticClass;

    public static function convertTagToId(string $tag): string
    {
        return strtolower(str_replace([' ', '\''], ['_', ''], $tag));
    }
}
