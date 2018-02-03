<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;

class Http404Page extends Page
{
    public function __construct(Connection $database)
    {
        parent::__construct($database, '404');
    }
}
