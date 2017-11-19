<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Top250\Shared\Algorithm;

final class SiteGenerator
{
    private $twig;
    private $database;
    private $logger;
    private $outPath;
    private $algorithm;
    private $weight;

    public function __construct(
        \Twig_Environment $twig,
        Connection $database,
        LoggerInterface $logger,
        string $outPath,
        Algorithm $algorithm,
        float $weight
    ) {
        $this->twig = $twig;
        $this->database = $database;
        $this->logger = $logger;
        $this->outPath = $outPath;
        $this->algorithm = $algorithm;
        $this->weight = $weight;
    }

    public function generate(): void
    {
        $this->logger->info(
            "Generating site using database: \"{$this->database->getParams()['path']}\""
            . " and \"$this->algorithm\" algorithm ($this->weight)."
        );

        $cursor = Queries::fetchTop250Games($this->database, $this->algorithm, $this->weight);
        $games = $cursor->fetchAll();

        if (!$games) {
            $this->logger->error('No games matching query.');

            return;
        }

        file_put_contents("$this->outPath", $this->twig->load('250.twig')->render(compact('games')));

        $this->logger->info("Page generated at: \"$this->outPath\".");
    }
}
