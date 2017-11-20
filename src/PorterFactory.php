<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Joomla\DI\Container;
use ScriptFUSION\Porter\Porter;
use ScriptFUSION\Porter\Provider\Steam\SteamProvider;

final class PorterFactory
{
    public function create(): Porter
    {
        $porter = new Porter($container = new Container);

        $container->set(SteamProvider::class, new SteamProvider);

        return $porter;
    }
}
