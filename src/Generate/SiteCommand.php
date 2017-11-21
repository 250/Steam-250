<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SiteCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('site')
            ->setDescription('Generate an HTML page from a template and data.')
            ->addArgument('db', InputArgument::REQUIRED, 'Path to database.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Output directory.', 'site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        (new SiteGeneratorFactory)->create($input->getArgument('db'))
            ->generate($input->getArgument('out'))
        ;

        return 0;
    }
}
