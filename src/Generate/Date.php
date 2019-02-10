<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\StaticClass;

final class Date
{
    use StaticClass;

    /**
     * Adaptively transforms the specified date, producing a relative or absolute date depending upon how recent it is.
     *
     * @param string|\DateTimeInterface $date Date.
     *
     * @return string Absolute or relative date.
     */
    public static function adapt($date): string
    {
        $date = is_string($date) ? new \DateTimeImmutable($date) : $date;
        $diff = $date->diff(new \DateTime);

        // Absolute.
        if ($diff->days > 30) {
            return $date->format('M Y');
        }

        // Relative.
        switch ($diff->days) {
            case 0:
                return 'today';

            case 1:
                return 'yesterday';
        }

        return "$diff->days days";
    }
}
