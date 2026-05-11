<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Tag;

final readonly class KeystoneTagChooser
{
    /**
     * @param array{
     *     name: string,
     *     category: 'g1'|'g2'|'g3'|'theme'|'feat'|'av'|'subj'|'plyr'|'in'|'r8'|'meta',
     *     votes: int,
     * } $tags
     */
    public static function choose(array $tags): string
    {
        array_walk($tags, static fn (&$tag) => $tag['score'] = $tag['votes'] * match ($tag['category']) {
            'g1' => .5,
            'g2' => 2,
            'g3' => 4,
            default => 1,
        });

        usort($tags, static fn ($a, $b) => $b['score'] <=> $a['score']);

        return reset($tags)['name'];
    }
}
