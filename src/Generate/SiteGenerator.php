<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Joomla\DI\Container;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;

final class SiteGenerator
{
    private $generator;
    private $pages;

    public function __construct(PageGenerator $generator, Container $pages)
    {
        // Drop any existing ranking data and migrate schema.
        Queries::recreateRankedListTable($pages->get('db'));

        $this->generator = $generator;
        $this->pages = $pages;
    }

    public function generate(string $outPath, string $prevDb = null): bool
    {
        foreach ($this->pages->getKeys() as $pageId) {
            /** @var Ranking $ranking */
            $ranking = $this->pages->buildObject($pageId);
            $ranking->setPrevDb($prevDb);

            if (!$this->generator->generate($ranking, $outPath)) {
                return false;
            }
        }

        return true;
    }
}
