<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\Generate;

use ScriptFUSION\Steam250\DatabaseFactory;
use ScriptFUSION\Steam250\TwigFactory;

final class GeneratorFactory
{
    public function create(): Generator
    {
        return new Generator(
            (new TwigFactory)->create(),
            (new DatabaseFactory)->create()
        );
    }
}
