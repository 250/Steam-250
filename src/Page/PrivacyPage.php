<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;

final class PrivacyPage extends Page
{
    public function __construct(Connection $database)
    {
        parent::__construct($database, 'privacy');
    }
}
