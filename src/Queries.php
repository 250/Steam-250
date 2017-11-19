<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use ScriptFUSION\Top250\Shared\Algorithm;

final class Queries
{
    private const TOP_250_GAMES = 'SELECT * FROM rank NATURAL JOIN app WHERE algorithm = ? ORDER BY rank';

    public static function fetchTop250Games(Connection $database, Algorithm $algorithm, float $weight): Statement
    {
        return $database->executeQuery(self::TOP_250_GAMES, ["$algorithm$weight"]);
    }
}
