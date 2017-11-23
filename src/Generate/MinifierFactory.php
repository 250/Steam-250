<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use voku\helper\HtmlMin;

final class MinifierFactory
{
    public function create(): HtmlMin
    {
        $minifier = new HtmlMin;

        // This is invalid. See: https://github.com/voku/HtmlMin/issues/6.
        $minifier->doRemoveHttpPrefixFromAttributes(false);

        // This is buggy. See: https://github.com/voku/HtmlMin/issues/9. Only saves 22 bytes anyway!
        $minifier->doRemoveWhitespaceAroundTags(false);

        return $minifier;
    }
}
