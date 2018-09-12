<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Log;

use ScriptFUSION\Steam250\SiteGenerator\Page\Page;

final class PageProcessor
{
    public function __invoke(array $record): array
    {
        $page = $record['context']['page'] ?? null;

        if ($page instanceof Page) {
            $record['message'] = "[{$page->getId()}] $record[message]";
        }

        return $record;
    }
}
