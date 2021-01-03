<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\PrimaryTagChooser;

class PreviewsPage extends Page
{
    private Connection $database;

    public function __construct(Connection $database)
    {
        parent::__construct($database, 'previews');

        $this->database = $database;
    }


    public function export(): array
    {
        $games = $this->database->executeQuery('
            SELECT *
            FROM app
            WHERE release_date > :now AND release_date <= :next_week AND total_reviews = 0 AND type = "game"
            ORDER BY release_date
        ', [
            'now' => time(),
            'next_week' => (new \DateTime('1 week'))->getTimestamp(),
        ])->fetchAllAssociative();

        // Decorate each game with tags.
        foreach ($games as &$game) {
            $game['primary_tag'] = PrimaryTagChooser::choose(
                $game['tags'] = Queries::fetchAppTags($this->database, +$game['id'])
            );
        }

        return compact('games') + parent::export();
    }
}
