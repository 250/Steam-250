<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class DatabaseFactory
{
    public function create(string $path): Connection
    {
        $connection = DriverManager::getConnection(['url' => "sqlite:///$path"]);
        self::defineCustomFunctions($connection->getWrappedConnection());

        $connection->exec(
            'CREATE TABLE IF NOT EXISTS rank (
                id INTEGER NOT NULL,
                algorithm TEXT NOT NULL,
                rank INTEGER NOT NULL,
                score REAL NOT NULL,
                PRIMARY KEY(algorithm, rank)
            );'
        );

        return $connection;
    }

    private static function defineCustomFunctions(\PDO $pdo): void
    {
        $pdo->sqliteCreateFunction('log10', 'log10', 1, \PDO::SQLITE_DETERMINISTIC);
        $pdo->sqliteCreateFunction('log', 'log', 2, \PDO::SQLITE_DETERMINISTIC);
        $pdo->sqliteCreateFunction('power', 'pow', 2, \PDO::SQLITE_DETERMINISTIC);
    }
}
