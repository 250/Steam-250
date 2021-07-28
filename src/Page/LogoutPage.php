<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;

class LogoutPage extends Page
{
    public function __construct(Connection $database)
    {
        parent::__construct($database, 'sync-logout');
    }
}
