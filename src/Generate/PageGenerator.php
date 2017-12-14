<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Rank\Ranker;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;
use voku\helper\HtmlMin;

final class PageGenerator
{
    private const RISERS_LIMIT = 5;

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

    public function generate(Toplist $toplist, string $outPath, string $prevDb = null): bool
    {
        $this->ranker->rank($toplist);

        $this->logger->info(
            "Generating \"{$toplist->getId()}\" page with database: \"{$this->database->getParams()['path']}\""
                . ($prevDb ? " and previous database: \"$prevDb\"" : '')
                . " using \"{$toplist->getAlgorithm()}\" algorithm ({$toplist->getWeight()})."
        );

        if (!$games = Queries::fetchRankedList($this->database, $toplist, $prevDb)) {
            $this->logger->error('No games matching query.');

            return false;
        }

        if ($prevDb) {
            $risers = $this->createRisersList($games);
            $fallers = $this->createFallersList($games);
        }

        $html = $this->twig->load("{$toplist->getTemplate()}.twig")->render(
            compact('games', 'toplist', 'risers', 'fallers')
        );

        if ($this->minify) {
            $this->logger->debug('Minifying HTML...');

            $html = $this->minifier->minify($html);
        }

        file_put_contents($out = "$outPath/{$toplist->getId()}.html", $html);

        $this->logger->info("Page generated at: \"$out\".");

        return true;
    }

    private function createRisersList(array $games): array
    {
        $games = array_filter($games, function (array $a) {
            return $a['movement'] > 0;
        });

        uasort($games, function (array $a, array $b) {
            return $b['movement'] <=> $a['movement'];
        });

        return \array_slice($games, 0, self::RISERS_LIMIT);
    }

    private function createFallersList(array $games): array
    {
        $games = array_filter($games, function (array $a) {
            return $a['movement'] < 0;
        });

        uasort($games, function (array $a, array $b) {
            return $a['movement'] <=> $b['movement'];
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
