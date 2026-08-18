<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\Shared\Tag\KeystoneTagChooser;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;

class PreviewsPage extends StaticPage
{
    private Connection $database;

    public function __construct(Connection $database)
    {
        parent::__construct($database);

        $this->database = $database;
    }

    public static function getStaticId(): string
    {
        return 'previews';
    }


    public function export(): array
    {
        $games = $this->database->executeQuery('
            SELECT *
            FROM app
            WHERE release_date > :now AND release_date <= :cutoff AND total_reviews = 0 AND type = "game"
            ORDER BY release_date
        ', [
            'now' => time(),
            'cutoff' => strtotime('30 day'),
        ])->fetchAllAssociative();

        // Decorate each game with its keystone tag.
        foreach ($games as &$game) {
            $game['keystone_tag'] = KeystoneTagChooser::choose(
                $game['tags'] = Queries::fetchAppTags($this->database, +$game['id'])
            );
        }

        return compact('games') + parent::export();
    }
}
