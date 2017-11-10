<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use ScriptFUSION\Top250\Shared\SharedQueries;

final class Queries
{
    private const TOP_250_GAMES =
        'SELECT *, '
        . SharedQueries::APP_SCORE
        . ' FROM review WHERE app_type = "game" ORDER BY score DESC LIMIT 250'
    ;

    public static function fetchTop250Games(Connection $database): Statement
    {
        return $database->executeQuery(self::TOP_250_GAMES);
    }
}
