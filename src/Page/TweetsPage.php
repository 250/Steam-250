<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;

class TweetsPage extends StaticPage
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    public static function getStaticId(): string
    {
        return 'cosmic_tweets';
    }
}
