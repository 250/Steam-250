<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Application;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\Tag;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigFactory
{
    public function create(string $ext): Environment
    {
        $twig = new Environment(
            new FilesystemLoader(Application::getAppPath('template')),
            [
                'strict_variables' => true,
            ]
        );

        $twig->addFilter(new TwigFilter('tag_id', [Tag::class, 'convertTagToId']));
        $twig->addFilter(new TwigFilter('adaptive_date', [Date::class, 'adapt']));
        $twig->addFunction(new TwigFunction('tz', static function (): string {
            return date_default_timezone_get();
        }));
        $twig->addGlobal('ext', $ext);
        $twig->addGlobal('patreon', 'https://www.patreon.com/steam250/overview');

        return $twig;
    }
}
