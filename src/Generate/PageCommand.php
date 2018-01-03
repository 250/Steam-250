<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Toplist\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\Toplist;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\ToplistFactory;
use ScriptFUSION\Steam250\SiteGenerator\Toplist\ToplistViolator;
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
            ->addOption('prev-db', null, InputOption::VALUE_REQUIRED, 'Previous database.')
            ->addOption('ext', null, InputOption::VALUE_REQUIRED, 'File extension in URLs.', '.html')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $generator = (new PageGeneratorFactory)->create($input->getArgument('db'), $input->getOption('ext'));
        $generator->setMinify($input->getOption('min'));

        /** @var Toplist $toplist */
        if (!$toplist =
            (new ToplistFactory($generator->getDatabase()))->create()->buildObject($id = $input->getArgument('list'))) {
            throw new \InvalidArgumentException("Invalid list ID: \"$id\".");
        }

        // Override algorithm and weight.
        ToplistViolator::violate(
            $toplist,
            Algorithm::memberOrNullByKey($input->getOption('algorithm'), false),
            (float)$input->getOption('weight')
        );

        return $generator->generate($toplist, $input->getArgument('out'), $input->getOption('prev-db')) ? 0 : 1;
    }
}
