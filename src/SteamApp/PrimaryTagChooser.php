<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\SteamApp;

use ScriptFUSION\StaticClass;

/**
 * Chooses the primary tag from a list of tags based on tag order and a blacklist.
 */
final class PrimaryTagChooser
{
    use StaticClass;

    private const BLACKLIST = [
        'Early Access',
        'Free to Play',
        'Indie',
        'Multiplayer',
        'RPGMaker',
        'VR',
    ];

    public static function choose(array $tags): ?string
    {
        $lastTag = end($tags);

        foreach ($tags as $tag) {
            if (!\in_array($tag, self::BLACKLIST, true)) {
                return $tag;
            }

            if ($tag === $lastTag) {
                return reset($tags);
            }
        }

        return null;
    }
}
