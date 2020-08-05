<?php


namespace App\Service;


use App\Service\RaceParser\GetRaces;
use League\Pipeline\Pipeline;

class RunParserService
{
    /**
     * @var Pipeline
     */
    private $pipeline;

    public function __construct(GetRaces $gr)
    {
        $this->pipeline = (new Pipeline())
            ->pipe($gr);

    }

    public function updateRuns()
    {
        return $this->pipeline->process(null);
    }
}