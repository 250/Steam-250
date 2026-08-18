<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;

class SearchPage extends StaticPage
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    public static function getStaticId(): string
    {
        return 'search';
    }
}
