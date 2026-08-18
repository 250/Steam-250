<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;

final class SyncGamesPage extends StaticPage
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    public static function getStaticId(): string
    {
        return 'sync-games';
    }
}
