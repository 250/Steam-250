<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Database;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static self ASC
 * @method static self DESC
 */
final class SortDirection extends AbstractEnumeration
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';
}
