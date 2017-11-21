<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Toplist;

use ScriptFUSION\Steam250\SiteGenerator\Database\SortDirection;

class Toplist
{
    private $template;
    private $algorithm;
    private $weight;
    private $limit;
    private $direction;

    public function __construct(
        string $template,
        Algorithm $algorithm,
        float $weight,
        int $limit,
        SortDirection $direction = null
    ) {
        $this->template = $template;
        $this->algorithm = $algorithm;
        $this->weight = $weight;
        $this->limit = $limit;
        $this->direction = $direction ?: SortDirection::DESC();
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getAlgorithm(): Algorithm
    {
        return $this->algorithm;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getDirection(): SortDirection
    {
        return $this->direction;
    }
}
