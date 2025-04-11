<?php

namespace App\Command;

use App\Service\RunParserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('update')]
class ParseRunsCommand extends Command
{
    public function __construct(
        private readonly RunParserService $rp
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Parse all runs')
            ->setHelp($this->getDescription())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->rp->updateRuns();

        return 0;
    }
}