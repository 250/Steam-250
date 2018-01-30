<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

abstract class Page
{
    private $id;
    private $template;

    abstract public function export(): array;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function setId(string $id)
    {
        $this->id = $id;
    }

    public function getTemplate(): string
    {
        return $this->template ?: $this->id;
    }

    protected function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}
