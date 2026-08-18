<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\Connection;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;

abstract class Page
{
    private Connection $database;
    private string $id;
    private string $template;

    public function __construct(Connection $database)
    {
        $this->database = $database;
        $this->useDefaultTemplate();
    }

    public function export(): array
    {
        $tags = Queries::fetchPopularTags($this->database);
        $tagCount = Queries::countTags($this->database);

        return compact('tags', 'tagCount') + ['CI' => $_SERVER['CI'] ?? false];
    }

    public function getId(): string
    {
        return $this->id ??= $this instanceof StaticId
            ? static::getStaticId()
            : throw new \LogicException(
                static::class . ' has no ID: implement ' . StaticId::class . ' or call setId().'
            )
        ;
    }

    protected function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTemplate(): string
    {
        return $this->template ?: $this->getId();
    }

    protected function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    protected function useDefaultTemplate(): void
    {
        $this->template = '';
    }
}
