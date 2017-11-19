<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use ScriptFUSION\Top250\Shared\Algorithm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('generate')
            ->setDescription('Generate Steam Top 250 site content from database.')
            ->addArgument('db', InputArgument::REQUIRED, 'Path to database.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Output file.', 'site/index.html')
            ->addOption('algorithm', 'a', InputOption::VALUE_REQUIRED, 'Ranking algorithm', Algorithm::WILSON)
            ->addOption('weight', 'w', InputOption::VALUE_REQUIRED, 'Algorithm-defined weighting.', 1.)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        (new SiteGeneratorFactory)->create(
            $input->getArgument('db'),
            $input->getArgument('out'),
            Algorithm::memberByKey($input->getOption('algorithm'), false),
            (float)$input->getOption('weight')
        )->generate();

        return 0;
    }
}
