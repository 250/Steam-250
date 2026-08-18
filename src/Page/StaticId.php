<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

/**
 * Provides a method to identify pages whose ID is statically known.
 */
interface StaticId
{
    public static function getStaticId(): string;
}
