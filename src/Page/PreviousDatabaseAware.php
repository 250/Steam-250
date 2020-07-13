<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

interface PreviousDatabaseAware
{
    public function setPrevDb(?string $prevDb): void;
}
