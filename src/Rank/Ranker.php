<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;

final class Ranker
{
    private $database;
    private $logger;
    private $algorithm;
    private $weight;

    public function __construct(
        Connection $database,
        LoggerInterface $logger,
        Algorithm $algorithm,
        float $weight
    ) {
        $this->database = $database;
        $this->logger = $logger;
        $this->algorithm = $algorithm;
        $this->weight = $weight;
    }

    public function decorate(int $targetCount = 250, string $targetType = 'game'): void
    {
        $this->logger->info(
            "Decorating up to $targetCount \"$targetType\" apps sorted by \"$this->algorithm\" ($this->weight)."
        );

        $matched = 0;
        $cursor = Queries::fetchAppsSortedByScore($this->database, $this->algorithm, $this->weight);

        while ($matched < $targetCount && false !== $app = $cursor->fetch()) {
            if (!isset($app['app_type'])) {
                $this->logger->debug("Fetching missing info for #$app[id] $app[app_name]");

                // Insert missing data into database.
                if (!$details = Decorator::decorate($this->database, +$app['id'], $this->logger)) {
                    continue;
                }

                // Update local state representation.
                $app = $details + $app;
            }

            if ($app['app_type'] === $targetType) {
                // Insert app rank into database.
                $this->database->executeQuery(
                    'INSERT OR REPLACE INTO rank (id, algorithm, rank, score) VALUES (?, ?, ?, ?)',
                    [
                        $app['id'],
                        "$this->algorithm$this->weight",
                        ++$matched,
                        $app['score'],
                    ]
                );
            }

            $this->logger->info("$matched/$targetCount #$app[id] ($app[app_name]) is $app[app_type].");
        }

        $this->logger->info('Finished :^)');
    }
}
