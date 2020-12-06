<?php


namespace App\Service;


use App\Service\RaceParser\ParseParticipants;
use App\Service\RaceParser\ParseRuns;
use App\Service\RaceParser\StoreRuns;
use League\Pipeline\Pipeline;

class RunParserService
{
    /**
     * @var Pipeline
     */
    private $pipeline;

    public function __construct(ParseRuns $gr, StoreRuns $sr, ParseParticipants $pp)
    {
        $this->pipeline = (new Pipeline())
            ->pipe($gr)
            ->pipe($pp)
            ->pipe($sr);
    }

    public function updateRuns()
    {
        return $this->pipeline->process(null);
    }
}