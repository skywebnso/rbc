<?php

namespace App\Console\Commands;

use App\Loader\NewsLoader;
use App\RbcNewsLoader;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParserNews extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'parser:news:load';

    /**
     * @var RbcNewsLoader
     */
    private $loader;

    public function __construct(RbcNewsLoader $loader)
    {
        $this->loader = $loader;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Loads news from rbc.ru.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        $this->loader->get();

        $this->line('All news are loaded.');

        return 0;
    }
}
