<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\Generate;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\Queries;

class Generator
{
    private $twig;

    private $database;

    public function __construct(\Twig_Environment $twig, Connection $database)
    {
        $this->twig = $twig;
        $this->database = $database;
    }

    public function generate(): void
    {
        $cursor = Queries::fetchTop250Games($this->database);
        $games = $cursor->fetchAll();

        file_put_contents('site/250.html', $this->twig->load('250.twig')->render(compact('games')));
    }
}
