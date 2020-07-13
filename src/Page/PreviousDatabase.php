<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

trait PreviousDatabase
{
    private ?string $prevDb;

    public function setPrevDb(?string $prevDb): void
    {
        $this->prevDb = $prevDb;
    }
}
