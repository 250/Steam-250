<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Impl\Annual100List;

final class ToplistFactory
{
    public function create(): Container
    {
        $container = new Container;

        foreach (ToplistName::members() as $name) {
            $container->alias($name->getAlias(), $name->getClassName());
        }

        foreach (range(Annual100List::EARLIEST_YEAR, date('Y')) as $year) {
            $container->set($year, function () use ($year): Toplist {
                return new Annual100List($year);
            });
        }

        return $container;
    }
}
