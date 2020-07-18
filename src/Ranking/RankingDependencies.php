<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Rank\Ranker;

final class RankingDependencies
{
    private Ranker $ranker;
    private Connection $database;
    private LoggerInterface $logger;

    public function __construct(Ranker $ranker, Connection $database, LoggerInterface $logger)
    {
        $this->ranker = $ranker;
        $this->database = $database;
        $this->logger = $logger;
    }

    public function getRanker(): Ranker
    {
        return $this->ranker;
    }

    public function getDatabase(): Connection
    {
        return $this->database;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
