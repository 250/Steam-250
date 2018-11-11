<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Application;
use ScriptFUSION\Steam250\SiteGenerator\ApplicationConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SiteCommand extends Command
{
    private $application;

    public function __construct(Application $application)
    {
        parent::__construct();

        $this->application = $application;
    }

    protected function configure(): void
    {
        $this
            ->setName('site')
            ->setDescription('Generates the entire site using a database.')
            ->addArgument('db', InputArgument::REQUIRED, 'Path to database.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Output directory.', 'site')
            ->addOption('min', null, InputOption::VALUE_NONE, 'Minify output.')
            ->addOption('prev-db', null, InputOption::VALUE_REQUIRED, 'Previous database.')
            ->addOption('ext', null, InputOption::VALUE_REQUIRED, 'File extension in URLs.', '.html')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->application->setConfig(new ApplicationConfig($input->getArgument('db')));

        return (new SiteGeneratorFactory)->create(
            $this->application->getContainer(),
            $input->getOption('ext'),
            $input->getOption('min')
        )
            ->generate($input->getArgument('out'), $input->getOption('prev-db')) ? 0 : 1
        ;
    }
}
