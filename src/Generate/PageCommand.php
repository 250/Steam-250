<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\CustomToplist;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\ToplistAliases;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('algorithm', 'a', InputOption::VALUE_REQUIRED, 'Ranking algorithm')
            ->addOption('weight', 'w', InputOption::VALUE_REQUIRED, 'Algorithm-defined weighting.')
            ->addOption('min', null, InputOption::VALUE_NONE, 'Minify output.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $toplist = new CustomToplist(
            ToplistAliases::createToplist($input->getArgument('list')),
            Algorithm::memberOrNullByKey($input->getOption('algorithm'), false),
            (float)$input->getOption('weight')
        );

        $generator = (new PageGeneratorFactory)->create($input->getArgument('db'));
        $generator->setMinify($input->getOption('min'));

        return $generator->generate($toplist, $input->getArgument('out')) ? 0 : 1;
    }
}
