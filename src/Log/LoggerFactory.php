<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Log;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public function create(string $name, bool $verbose): LoggerInterface
    {
        /** @var Logger $logger */
        $logger = (new \ScriptFUSION\Steam250\Log\LoggerFactory)->create($name, $verbose);

        return $logger->pushProcessor(new PageProcessor);
    }
}
