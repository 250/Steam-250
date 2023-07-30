<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Tag;

use MessagePack\Packer;

final class TagDirectoryStatePacker
{
    public static function packSingleCategory(int $categoryId): string
    {
        $packer = new Packer();

        return rtrim(base64_encode($packer->packInt(0) . $packer->packInt(1 << $categoryId)), '=');
    }
}
