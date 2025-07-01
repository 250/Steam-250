<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Log;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use ScriptFUSION\Steam250\SiteGenerator\Page\Page;

final class PageProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $page = $record['context']['page'] ?? null;

        if ($page instanceof Page) {
            return $record->with(message: "[{$page->getId()}] $record[message]");
        }

        return $record;
    }
}
