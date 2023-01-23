<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use voku\helper\HtmlMin;

final class MinifierFactory
{
    public function create(): HtmlMin
    {
        return new HtmlMin;
    }
}
