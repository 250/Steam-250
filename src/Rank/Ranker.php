<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

final class Ranker
{
    private $database;
    private $logger;

    public function __construct(Connection $database, LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function rank(Toplist $toplist): void
    {
        $this->logger->info(
            "Ranking up to {$toplist->getLimit()} games"
                . " sorted by \"{$toplist->getAlgorithm()}\" ({$toplist->getWeight()})"
                . " from database: \"{$this->database->getParams()['path']}\"."
        );

        $matched = 0;
        $cursor = Queries::rankList($this->database, $toplist);

        while (false !== $app = $cursor->fetch()) {
            // Insert app rank into database.
            $this->database->executeQuery(
                'INSERT OR REPLACE INTO rank (list_id, rank, app_id, score) VALUES (?, ?, ?, ?)',
                [
                    $toplist->getId(),
                    ++$matched,
                    $app['id'],
                    $app['score'] ?: 0,
                ]
            );
        }

        $this->logger->info("Finished ranking $matched games.");
    }
}
