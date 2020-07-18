<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Generate;

use ScriptFUSION\Steam250\SiteGenerator\Application;
use ScriptFUSION\Steam250\SiteGenerator\ApplicationConfig;
use ScriptFUSION\Steam250\SiteGenerator\Page\PreviousDatabaseAware;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Algorithm;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\PageContainerFactory;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class PageCommand extends Command
{
    private Application $application;

    public function __construct(Application $application)
    {
        parent::__construct();

        $this->application = $application;
    }

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
        $this->application->setConfig(new ApplicationConfig($input->getArgument('db')));

        $generator = (new PageGeneratorFactory)->create($input->getOption('ext'));
        $generator->setMinify($input->getOption('min'));

        /** @var Ranking $page */
        if (!$page = (new PageContainerFactory)->create($this->application->getContainer())
            ->buildObject($id = $input->getArgument('list'))) {
            throw new \InvalidArgumentException("Invalid page ID: \"$id\".");
        }

        if ($page instanceof PreviousDatabaseAware) {
            $page->setPrevDb($input->getOption('prev-db'));
        }
        if ($page instanceof Ranking) {
            // Override algorithm and weight.
            if ($algorithm = $input->getOption('algorithm')) {
                $page->setAlgorithm(Algorithm::memberOrNullByKey($algorithm, false));
            }
            if ($weight = $input->getOption('weight')) {
                $page->setWeight((float)$weight);
            }
        }

        return $generator->generate($page, $input->getArgument('out')) ? 0 : 1;
    }
}
