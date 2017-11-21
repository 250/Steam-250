<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static WILSON()
 * @method static BAYESIAN()
 * @method static TORN()
 * @method static LAPLACE()
 * @method static LAPLACE_LOG()
 * @method static DIRICHLET_PRIOR()
 * @method static DIRICHLET_PRIOR_LOG()
 */
final class Algorithm extends AbstractEnumeration
{
    public const WILSON = 'WILSON';
    public const BAYESIAN = 'BAYESIAN';
    public const TORN = 'TORN';
    public const LAPLACE = 'LAPLACE';
    public const LAPLACE_LOG = 'LAPLACE_LOG';
    public const DIRICHLET_PRIOR = 'DIRICHLET_PRIOR';
    public const DIRICHLET_PRIOR_LOG = 'DIRICHLET_PRIOR_LOG';
}
