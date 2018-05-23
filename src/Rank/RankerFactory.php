<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\Shared\Log\LoggerFactory;

final class RankerFactory
{
    public function create(Connection $connection): Ranker
    {
        return new Ranker(
            $connection,
            (new LoggerFactory)->create('Ranker', false)
        );
    }
}
