<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Rank\Ranker;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;

final class PageGenerator
{
    private $twig;
    private $database;
    private $ranker;
    private $logger;

    public function __construct(\Twig_Environment $twig, Connection $database, Ranker $ranker, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->database = $database;
        $this->ranker = $ranker;
        $this->logger = $logger;
    }

    public function generate(Toplist $toplist, string $outPath): void
    {
        $this->ranker->rank($toplist);

        $this->logger->info(
            "Generating \"{$toplist->getTemplate()}\" page using database: \"{$this->database->getParams()['path']}\""
                . " and \"{$toplist->getAlgorithm()}\" algorithm ({$toplist->getWeight()})."
        );

        $cursor = Queries::fetchRankedList($this->database, $toplist);
        $games = $cursor->fetchAll();

        if (!$games) {
            $this->logger->error('No games matching query.');

            return;
        }

        file_put_contents(
            $out = "$outPath/{$toplist->getTemplate()}.html",
            $this->twig->load("{$toplist->getTemplate()}.twig")->render(compact('games', 'toplist'))
        );

        $this->logger->info("Page generated at: \"$out\".");
    }
}
