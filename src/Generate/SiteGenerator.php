<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;

final class SiteGenerator
{
    private $generator;
    private $pages;

    public function __construct(PageGenerator $generator, Container $pages)
    {
        // Drop any existing ranking data and migrate schema.
        Queries::recreateRankedListTable($generator->getDatabase());

        $this->generator = $generator;
        $this->pages = $pages;
    }

    public function generate(string $outPath, string $prevDb = null): bool
    {
        foreach ($this->pages->getKeys() as $pageId) {
            if (!$this->generator->generate($this->pages->buildObject($pageId), $outPath, $prevDb)) {
                return false;
            }
        }

        return true;
    }
}
