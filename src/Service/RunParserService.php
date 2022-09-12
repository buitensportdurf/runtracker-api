<?php


namespace App\Service;


use App\Service\RaceParser\ParseParticipants;
use App\Service\RaceParser\ParseRuns;
use App\Service\RaceParser\StoreRuns;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineBuilder;
use League\Pipeline\PipelineInterface;

class RunParserService
{
    private PipelineInterface $pipeline;

    public function __construct(
        ParseRuns         $gr,
        StoreRuns         $sr,
        ParseParticipants $pp,
    )
    {
        $builder = new PipelineBuilder();
        $this->pipeline = $builder
            ->add($gr)
//            ->add($pp)
//            ->add($sr)
            ->build();
    }

    public function updateRuns()
    {
        return $this->pipeline->process(null);
    }
}