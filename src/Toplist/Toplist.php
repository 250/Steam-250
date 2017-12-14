<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use Doctrine\DBAL\Query\QueryBuilder;

abstract class Toplist
{
    private $id;
    private $limit;
    private $algorithm;
    private $weight;
    private $template;

    public function __construct(string $id, int $limit, Algorithm $algorithm = null, float $weight = null)
    {
        $this->id = $id;
        $this->limit = $limit;
        $this->algorithm = $algorithm;
        $this->weight = $weight;
    }

    abstract public function customizeQuery(QueryBuilder $builder): void;

    public function getId(): string
    {
        return $this->id;
    }

    protected function setId(string $id)
    {
        $this->id = $id;
    }

    public function getAlgorithm(): ?Algorithm
    {
        return $this->algorithm;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getLimit(): int
    {
        return $this->limit;
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
