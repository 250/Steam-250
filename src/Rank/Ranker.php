<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

final class Ranker
{
    private $database;
    private $logger;

    public function __construct(Connection $database, LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function rank(Ranking $ranking): void
    {
        $this->logger->info(
            "Ranking up to {$ranking->getLimit()} games"
                . " sorted by \"{$ranking->getAlgorithm()}\" ({$ranking->getWeight()})"
                . " from database: \"{$this->database->getParams()['path']}\"."
        );

        $matched = 0;
        $cursor = Queries::rankList($this->database, $ranking);

        while (false !== $app = $cursor->fetch()) {
            // Insert app rank into database.
            $this->database->executeQuery(
                'INSERT OR REPLACE INTO rank (list_id, rank, app_id, score, owner) VALUES (?, ?, ?, ?, ?)',
                [
                    $ranking->getId(),
                    ++$matched,
                    $app['id'],
                    $app['score'] ?? null,
                    $app['owner'] ?? null,
                ]
            );
        }

        $this->logger->info("Finished ranking $matched games.");
    }
}
