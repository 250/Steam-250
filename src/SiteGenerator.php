<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

final class SiteGenerator
{
    private $twig;

    private $database;

    private $logger;

    private $outPath;

    public function __construct(\Twig_Environment $twig, Connection $database, LoggerInterface $logger, string $outPath)
    {
        $this->twig = $twig;
        $this->database = $database;
        $this->logger = $logger;
        $this->outPath = $outPath;
    }

    public function generate(): void
    {
        $this->logger->info("Generating site using database: \"{$this->database->getParams()['path']}\".");

        $cursor = Queries::fetchTop250Games($this->database);
        $games = $cursor->fetchAll();

        file_put_contents("$this->outPath/index.html", $this->twig->load('250.twig')->render(compact('games')));

        $this->logger->info("Site generated in: \"$this->outPath\".");
    }
}
