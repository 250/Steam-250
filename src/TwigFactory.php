<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

final class TwigFactory
{
    public function create(): \Twig_Environment
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem(Application::getAppPath('template')),
            [
                'strict_variables' => true,
            ]
        );
    }
}
