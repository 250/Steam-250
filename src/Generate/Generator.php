<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\Generate;

use Doctrine\DBAL\Connection;

class Generator
{
    private $twig;

    private $database;

    public function __construct(\Twig_Environment $twig, Connection $database)
    {
        $this->twig = $twig;
        $this->database = $database;
    }

    public function generate(): void
    {
        $cursor = $this->database->executeQuery(
            'SELECT *,
                (
                    (positive_reviews + 1.9208) / total_reviews - 1.96
                        * SQRT((positive_reviews * negative_reviews) / total_reviews + 0.9604)
                        / total_reviews
                ) / (1 + 3.8416 / total_reviews) AS score
             FROM review
             ORDER BY score DESC
             LIMIT 250'
        );

        $games = $cursor->fetchAll();

        file_put_contents('site/250.html', $this->twig->load('250.twig')->render(compact('games')));
    }
}
