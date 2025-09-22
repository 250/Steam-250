<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteFormatter;
use ScriptFUSION\Byte\Unit\SymbolDecorator;
use ScriptFUSION\Steam250\SiteGenerator\Application;
use ScriptFUSION\Steam250\SiteGenerator\Tag\Tag;
use ScriptFUSION\Type\Date;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigFactory
{
    public function create(string $ext): Environment
    {
        $twig = new Environment(
            $loader = new FilesystemLoader(Application::getAppPath('template')),
            [
                'strict_variables' => true,
            ]
        );
        $loader->addPath(Application::getAppPath('vendor/250/components'), 'components');

        $twig->addFilter(new TwigFilter('tag_id', [Tag::class, 'convertTagToId']));
        $twig->addFilter(new TwigFilter('adaptive_date', [Date::class, 'adapt']));
        $twig->addFilter(new TwigFilter('unit_format', fn (int|float $bytes) => new ByteFormatter(
                new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)->setPrefixes('KMBT')
            )
                ->setBase(Base::DECIMAL)
                ->setFormat('%v%u')
                ->format($bytes)
        ));
        $twig->addFilter(new TwigFilter('sigfig', static fn (int|float $number, int $sigfigs): string =>
            new ByteFormatter(
                new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)
            )
                ->setBase(Base::DECIMAL)
                ->setSignificantFigures($sigfigs)
                ->format($number)
        ));
        $twig->addFunction(new TwigFunction('tz', fn () => date_default_timezone_get()));
        $twig->addGlobal('ext', $ext);
        $twig->addGlobal('club250', $_ENV['CLUB_250_BASE_URL']);
        $twig->addGlobal('club250_static', $_ENV['CLUB_250_STATIC_BASE_URL']);
        $twig->addGlobal('patreon', 'https://www.patreon.com/steam250/overview');
        $twig->addGlobal('discord', 'https://discord.steam250.com');

        return $twig;
    }
}
