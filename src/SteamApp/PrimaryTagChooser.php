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

    public static function choose(array $tags): ?array
    {
        $lastTag = end($tags);

        foreach ($tags as $id => $name) {
            if (!\in_array($name, self::BLACKLIST, true)) {
                return compact('id', 'name');
            }

            if ($name === $lastTag) {
                return ['name' => reset($tags), 'id' => key($tags)];
            }
        }

        return null;
    }
}
