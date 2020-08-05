<?php


namespace App\Service\RaceParser;


use App\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use League\Pipeline\StageInterface;

class StoreRaces implements StageInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * StoreRaces constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke($races)
    {
        foreach ($races as $rawRace) {
            dump($rawRace['city']);
            $repo = $this->em->getRepository(Race::class);
            // First try to find existing race
            $date = \DateTime::createFromFormat('d-m-Y', $rawRace['date']);
            if($repo->findBy(['date' => $date, 'city' => $rawRace['city']])){
                continue;
            }
        }
        return null;
    }
}