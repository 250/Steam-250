<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Pdo\Sqlite;

final class DatabaseFactory
{
    public function create(string $path): Connection
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $path]);
        self::defineCustomFunctions($connection->getNativeConnection());
        Queries::createRankedListTable($connection);

        return $connection;
    }

    public function createAppMediaCache(string $path): Connection
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $path]);

        self::defineCustomFunctions($connection->getNativeConnection());
        Queries::createAppMediaTable($connection);

        return $connection;
    }

    private static function defineCustomFunctions(Sqlite $pdo): void
    {
        $pdo->createFunction('log10', 'log10', 1, $pdo::DETERMINISTIC);
        $pdo->createFunction('log', 'log', 2, $pdo::DETERMINISTIC);
        $pdo->createFunction('power', 'pow', 2, $pdo::DETERMINISTIC);
        $pdo->createFunction('sin', 'sin', 1, $pdo::DETERMINISTIC);
        $pdo->createFunction('pi', 'pi', 0, $pdo::DETERMINISTIC);
    }
}
