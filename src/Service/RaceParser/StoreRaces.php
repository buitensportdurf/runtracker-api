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

    public function __invoke($rawRaces)
    {
        $races = [];
        foreach ($rawRaces as $rawRace) {
            $repo = $this->em->getRepository(Race::class);
            // First try to find existing race
            if (!($race = $repo->findOneBy(['date' => $rawRace['date'], 'city' => $rawRace['city']]))) {
                $race = (new Race())
                    ->setDate($rawRace['date'])
                    ->setCity($rawRace['city'])
                    ->setCircuits($rawRace['circuits'])
                    ->setDistances($rawRace['distances'])
                    ->setOrganizer($rawRace['org']['url'])
                    ->setAge($rawRace['age'])
                    ->setSubscribe($rawRace['subscriber'] ?? null)
                    ->setResult($rawRace['result'] ?? null);

                $this->em->persist($race);
            }
            $races[] = $race;
        }
        $this->em->flush();
        return $races;
    }
}