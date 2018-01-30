<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Psr\Log\LoggerInterface;
use ScriptFUSION\Steam250\SiteGenerator\Page\Page;
use voku\helper\HtmlMin;

final class PageGenerator
{
    private $twig;
    private $logger;
    private $minifier;
    private $minify = false;

    public function __construct(
        \Twig_Environment $twig,
        LoggerInterface $logger,
        HtmlMin $minifier
    ) {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->minifier = $minifier;
    }

    public function generate(Page $page, string $outPath): bool
    {
        try {
            $export = $page->export();
        } catch (\Exception $exception) {
            $this->logger->error("Exception encountered: \"$exception\"");

            return false;
        }

        $html = $this->twig->load("{$page->getTemplate()}.twig")->render($export);

        if ($this->minify) {
            $this->logger->debug('Minifying HTML...');

            $html = $this->minifier->minify($html);
        }

        $this->ensurePathExists($out = "$outPath/{$page->getId()}.html");
        file_put_contents($out, $html);
        $this->logger->info("Page generated at: \"$out\".");

        return true;
    }

    private function ensurePathExists(string $path): void
    {
        if (!is_dir($dir = \dirname($path)) && !mkdir($dir) && !is_dir($dir)) {
            throw new \RuntimeException("Could not create directory: \"$dir\".");
        }
    }

    public function setMinify(bool $minify)
    {
        $this->minify = $minify;
    }
}
