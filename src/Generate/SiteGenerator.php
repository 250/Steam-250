<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;

final class SiteGenerator
{
    private $generator;
    private $toplists;

    public function __construct(PageGenerator $generator, Container $toplists)
    {
        // Drop any existing ranking data and migrate schema.
        Queries::recreateRankedListTable($generator->getDatabase());

        $this->generator = $generator;
        $this->toplists = $toplists;
    }

    public function generate(string $outPath, string $prevDb = null): bool
    {
        foreach ($this->toplists->getKeys() as $listId) {
            if (!$this->generator->generate($this->toplists->buildObject($listId), $outPath, $prevDb)) {
                return false;
            }
        }

        return true;
    }
}
