<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Porter\Provider\Steam\Resource\InvalidAppIdException;
use ScriptFUSION\Porter\Provider\Steam\Scrape\ParserException;
use ScriptFUSION\Steam250\SiteGenerator\PorterFactory;

/**
 * Decorates Steam games with missing information, such as whether they're actually a game.
 *
 * We could just decorate every app but it would take too long so we deliberately decorate the minimum necessary to
 * build a complete list for our use cases, such as the top 250 list.
 */
final class Decorator
{
    public static function decorate(Connection $database, int $appId, LoggerInterface $logger): ?array
    {
        try {
            // Import missing data.
            $details = (new PorterFactory)->create()->importOne(new AppDetailsSpecification($appId));
        } catch (InvalidAppIdException | ParserException $exception) {
            // App ID hidden, obsolete or region locked.
            $logger->warning($exception->getMessage());

            return null;
        }

        // Update database.
        $database->update('app', $details, ['id' => $appId]);

        return $details;
    }
}
