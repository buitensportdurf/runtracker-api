<?php


namespace App\Service;


use App\Service\RaceParser\GetRaces;
use App\Service\RaceParser\StoreRaces;
use League\Pipeline\Pipeline;

class RunParserService
{
    /**
     * @var Pipeline
     */
    private $pipeline;

    public function __construct(GetRaces $gr, StoreRaces $sr)
    {
        $this->pipeline = (new Pipeline())
            ->pipe($gr)
            ->pipe($sr);

    }

    public function updateRuns()
    {
        return $this->pipeline->process(null);
    }
}