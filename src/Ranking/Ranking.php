<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Generate\CustomizeGames;
use ScriptFUSION\Steam250\SiteGenerator\Page\Page;
use ScriptFUSION\Steam250\SiteGenerator\Page\PreviousDatabase;
use ScriptFUSION\Steam250\SiteGenerator\Page\PreviousDatabaseAware;
use ScriptFUSION\Steam250\SiteGenerator\Rank\Ranker;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\PrimaryTagChooser;

abstract class Ranking extends Page implements PreviousDatabaseAware
{
    use PreviousDatabase;

    private const RISERS_LIMIT = 10;

    private Ranker $ranker;
    private Connection $database;
    private LoggerInterface $logger;
    private int $limit;
    private ?Algorithm $algorithm;
    private ?float $weight;
    private string $title = '';
    private string $description = '';

    public function __construct(
        RankingDependencies $dependencies,
        string $id,
        int $limit,
        Algorithm $algorithm = null,
        float $weight = null
    ) {
        parent::__construct($dependencies->getDatabase(), $id);

        $this->ranker = $dependencies->getRanker();
        $this->database = $dependencies->getDatabase();
        $this->logger = $dependencies->getLogger();
        $this->limit = $limit;
        $this->algorithm = $algorithm;
        $this->weight = $weight;
    }

    abstract public function customizeQuery(QueryBuilder $builder): void;

    public function export(): array
    {
        $this->ranker->rank($this);

        $this->logger->info(
            "Generating page with database: \"{$this->database->getParams()['path']}\""
            . ($this->prevDb ? " and previous database: \"$this->prevDb\"" : '')
            . " using \"{$this->getAlgorithm()}\" algorithm ({$this->getWeight()}).",
            ['page' => $this]
        );

        if (!$games = Queries::fetchRankedList($this->database, $this, $this->prevDb)) {
            $this->logger->error($error = 'No games matching query.');

            throw new \RuntimeException($error);
        }

        // Decorate each game with tags.
        foreach ($games as &$game) {
            $game['primary_tag'] = PrimaryTagChooser::choose(
                $game['tags'] = Queries::fetchAppTags($this->database, +$game['id'])
            );
        }
        if ($this instanceof CustomizeGames) {
            $this->customizeGames($games, $this->database);
        }

        if ($this->prevDb) {
            $risers = $this->createRisersList($games);
            $fallers = $this->createFallersList($games);
            $new = $this->createNewEntriesList($games);
            $missing = Queries::fetchMissingApps($this->database, $this, self::RISERS_LIMIT, $this->prevDb);
        }

        return compact('games', 'risers', 'fallers', 'new', 'missing') + ['ranking' => $this] + parent::export();
    }

    private function createRisersList(array $games): array
    {
        $games = array_filter($games, static function (array $a): bool {
            return $a['movement'] > 0;
        });

        uasort($games, static function (array $a, array $b): int {
            return $b['movement'] <=> $a['movement'] ?: $a['rank'] <=> $b['rank'];
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    private function createFallersList(array $games): array
    {
        $games = array_filter($games, static function (array $a): bool {
            return $a['movement'] < 0;
        });

        uasort($games, static function (array $a, array $b): int {
            return $a['movement'] <=> $b['movement'] ?: $a['rank'] <=> $b['rank'];
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    private function createNewEntriesList(array $games): array
    {
        $games = array_filter($games, static function (array $a): bool {
            return $a['movement'] === null;
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    public function getAlgorithm(): ?Algorithm
    {
        return $this->algorithm;
    }

    public function setAlgorithm(?Algorithm $algorithm): void
    {
        $this->algorithm = $algorithm;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
