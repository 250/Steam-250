<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\Connection;
use Joomla\DI\Container;
use ScriptFUSION\Steam250\Shared\Log\LoggerFactory;
use ScriptFUSION\Steam250\SiteGenerator\Database\DatabaseFactory;
use ScriptFUSION\Steam250\SiteGenerator\Generate\PageCommand;
use ScriptFUSION\Steam250\SiteGenerator\Generate\SiteCommand;
use ScriptFUSION\Steam250\SiteGenerator\Rank\Ranker;
use ScriptFUSION\Steam250\SiteGenerator\Rank\RankerFactory;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\RankingDependencies;

final class Application
{
    private $cli;
    private $config;
    private $container;

    public function __construct()
    {
        $this->cli = $cli = new \Symfony\Component\Console\Application;

        $cli->addCommands([
            new SiteCommand($this),
            new PageCommand($this),
        ]);
    }

    public function start(): int
    {
        return $this->cli->run();
    }

    public function getContainer(): Container
    {
        $container = $this->container ?: $this->container = new Container;

        $container->alias('db', Connection::class);

        $container->share(Connection::class, (new DatabaseFactory)->create($this->getConfig()->getDbPath()));
        $container->share(Ranker::class, (new RankerFactory)->create($container->get('db')));
        $container->share(
            RankingDependencies::class,
            new RankingDependencies(
                $container->get(Ranker::class),
                $container->get('db'),
                (new LoggerFactory)->create('Ranking', false)
            ),
            true
        );

        return $container;
    }

    public function getConfig(): ApplicationConfig
    {
        return $this->config;
    }

    public function setConfig(ApplicationConfig $config): void
    {
        $this->config = $config;
    }

    public static function getAppPath(string $path): string
    {
        return __DIR__ . "/../$path";
    }
}
