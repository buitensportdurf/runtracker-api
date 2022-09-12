<?php


namespace App\Command;


use App\Service\RunParserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseRunsCommand extends Command
{
    protected static $defaultName = 'update';

    /**
     * @var RunParserService
     */
    private $rp;

    /**
     * ParseRunsCommand constructor.
     * @param RunParserService $rp
     */
    public function __construct(RunParserService $rp)
    {
        parent::__construct();

        $this->rp = $rp;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Parse all runs')
            ->setHelp($this->getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runs = $this->rp->updateRuns();
        dump($runs);

        return 0;
    }
}