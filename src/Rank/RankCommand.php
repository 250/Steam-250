<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use ScriptFUSION\Steam250\SiteGenerator\Algorithm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class RankCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('rank')
            ->setDescription('Rank Top 250 games, decorating database as necessary.')
            ->addArgument('db', InputArgument::REQUIRED, 'Path to database.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Output file.', 'site/index.html')
            ->addOption('algorithm', 'a', InputOption::VALUE_REQUIRED, 'Ranking algorithm', Algorithm::WILSON)
            ->addOption('weight', 'w', InputOption::VALUE_REQUIRED, 'Algorithm-defined weighting.', 1.)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        (new RankerFactory)->create(
            $input->getArgument('db'),
            Algorithm::memberByKey($input->getOption('algorithm'), false),
            (float)$input->getOption('weight')
        )->decorate();

        return 0;
    }
}
