<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Doctrine\DBAL\Connection;

interface CustomizeGames
{
    public function customizeGames(array &$games, Connection $database): void;
}
