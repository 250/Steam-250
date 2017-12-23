<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Container;

use Joomla\DI\Container;

class EnumerableContainer extends Container implements \IteratorAggregate
{
    public function getIterator()
    {
        foreach (array_unique(array_merge($this->aliases, array_keys($this->resources))) as $key) {
            yield $key;
        }
    }
}
