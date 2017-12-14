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
        Queries::createRankedListTable($connection);

        return $connection;
    }

    private static function defineCustomFunctions(\PDO $pdo): void
    {
        $pdo->sqliteCreateFunction('log10', 'log10', 1, \PDO::SQLITE_DETERMINISTIC);
        $pdo->sqliteCreateFunction('log', 'log', 2, \PDO::SQLITE_DETERMINISTIC);
        $pdo->sqliteCreateFunction('power', 'pow', 2, \PDO::SQLITE_DETERMINISTIC);
    }
}
