<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\PageContainerFactory;

final class SiteGeneratorFactory
{
    public function create(Container $container, string $extension, bool $minify): SiteGenerator
    {
        $generator = (new PageGeneratorFactory)->create($extension);
        $generator->setMinify($minify);

        return new SiteGenerator($generator, (new PageContainerFactory)->create($container));
    }
}
