<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\Database\Queries;

class SiteGenerator
{
    private $twig;

    private $database;

    private $outPath;

    public function __construct(\Twig_Environment $twig, Connection $database, string $outPath)
    {
        $this->twig = $twig;
        $this->database = $database;
        $this->outPath = $outPath;
    }

    public function generate(): void
    {
        $cursor = Queries::fetchTop250Games($this->database);
        $games = $cursor->fetchAll();

        file_put_contents("$this->outPath/index.html", $this->twig->load('250.twig')->render(compact('games')));
    }
}
