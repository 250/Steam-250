<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Ranking;

/**
 * The exception that is thrown when an empty ranking has been detected and thus this page should not be generated.
 */
final class SkipEmptyRankingException extends \RuntimeException
{
    // Intentionally empty.
}
