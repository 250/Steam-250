<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('generate')
            ->setDescription('Generate Steam Top 250 site content from database.')
            ->addArgument('db', InputArgument::REQUIRED, 'Path to database.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Output directory.', 'site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        (new SiteGeneratorFactory)->create(
            $input->getArgument('db'),
            $input->getArgument('out')
        )->generate();

        return 0;
    }
}
