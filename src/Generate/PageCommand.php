<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Toplist\ToplistAliases;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PageCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('page')
            ->setDescription('Generate an HTML page from a template and data.')
            ->addArgument('list', InputArgument::REQUIRED, 'List name.')
            ->addArgument('db', InputArgument::REQUIRED, 'Path to database.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Output directory.', 'site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        (new PageGeneratorFactory)->create($input->getArgument('db'))
            ->generate(ToplistAliases::createToplist($input->getArgument('list')), $input->getArgument('out'))
        ;

        return 0;
    }
}
