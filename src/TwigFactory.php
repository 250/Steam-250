<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

final class TwigFactory
{
    public function create(string $ext): \Twig_Environment
    {
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(Application::getAppPath('template')),
            [
                'strict_variables' => true,
            ]
        );

        $twig->addFilter(new \Twig_Filter('tag_id', [Tag::class, 'convertTagToId']));
        $twig->addGlobal('ext', $ext);

        return $twig;
    }
}
