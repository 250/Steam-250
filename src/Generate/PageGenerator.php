<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Rank\Ranker;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\SteamApp\PrimaryTagChooser;
use voku\helper\HtmlMin;

final class PageGenerator
{
    private const RISERS_LIMIT = 10;

    private $twig;
    private $database;
    private $ranker;
    private $logger;
    private $minifier;
    private $minify = false;

    public function __construct(
        \Twig_Environment $twig,
        Connection $database,
        Ranker $ranker,
        LoggerInterface $logger,
        HtmlMin $minifier
    ) {
        $this->twig = $twig;
        $this->database = $database;
        $this->ranker = $ranker;
        $this->logger = $logger;
        $this->minifier = $minifier;
    }

    public function generate(Ranking $ranking, string $outPath, string $prevDb = null): bool
    {
        $this->ranker->rank($ranking);

        $this->logger->info(
            "Generating \"{$ranking->getId()}\" page with database: \"{$this->database->getParams()['path']}\""
                . ($prevDb ? " and previous database: \"$prevDb\"" : '')
                . " using \"{$ranking->getAlgorithm()}\" algorithm ({$ranking->getWeight()})."
        );

        if (!$games = Queries::fetchRankedList($this->database, $ranking, $prevDb)) {
            $this->logger->error('No games matching query.');

            return false;
        }

        // Decorate each game with tags.
        foreach ($games as &$game) {
            $game['primary_tag'] = PrimaryTagChooser::choose(
                $game['tags'] = Queries::fetchAppTags($this->database, +$game['id'])
            );
        }
        if ($ranking instanceof CustomizeGames) {
            $ranking->customizeGames($games, $this->database);
        }

        $tags = Queries::fetchPopularTags($this->database);

        if ($prevDb) {
            $risers = $this->createRisersList($games);
            $fallers = $this->createFallersList($games);
            $new = $this->createNewEntriesList($games);
        }

        $html = $this->twig->load("{$ranking->getTemplate()}.twig")->render(
            compact('games', 'ranking', 'tags', 'risers', 'fallers', 'new')
        );

        if ($this->minify) {
            $this->logger->debug('Minifying HTML...');

            $html = $this->minifier->minify($html);
        }

        $this->ensurePathExists($out = "$outPath/{$ranking->getId()}.html");
        file_put_contents($out, $html);
        $this->logger->info("Page generated at: \"$out\".");

        return true;
    }

    private function ensurePathExists(string $path): void
    {
        if (!is_dir($dir = \dirname($path)) && !mkdir($dir) && !is_dir($dir)) {
            throw new \RuntimeException("Could not create directory: \"$dir\".");
        }
    }

    private function createRisersList(array $games): array
    {
        $games = array_filter($games, function (array $a): bool {
            return $a['movement'] > 0;
        });

        uasort($games, function (array $a, array $b): int {
            return $b['movement'] <=> $a['movement'];
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    private function createFallersList(array $games): array
    {
        $games = array_filter($games, function (array $a): bool {
            return $a['movement'] < 0;
        });

        uasort($games, function (array $a, array $b): int {
            return $a['movement'] <=> $b['movement'];
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    private function createNewEntriesList(array $games): array
    {
        $games = array_filter($games, function (array $a): bool {
            return $a['movement'] === null;
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    public function setMinify(bool $minify)
    {
        $this->minify = $minify;
    }

    public function getDatabase(): Connection
    {
        return $this->database;
    }
}
