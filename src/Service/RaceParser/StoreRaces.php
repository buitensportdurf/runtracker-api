<?php


namespace App\Service\RaceParser;


use App\Entity\Organization;
use App\Entity\Run;
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
            $runRepo = $this->em->getRepository(Run::class);
            // First try to find existing run
            if (!($run = $runRepo->findOneBy(['date' => $rawRace['date'], 'city' => $rawRace['city']]))) {
                // Get the organization or create if not already exists
                $organizationRepo = $this->em->getRepository(Organization::class);
                if (!($organization = $organizationRepo->findOneBy(['name' => $rawRace['org']['name']]))) {
                    $organization = (new Organization())
                        ->setName($rawRace['org']['name'])
                        ->setWebsite($rawRace['org']['url']);
                    $this->em->persist($organization);
                    $this->em->flush(); // Flush to make sure we don't get duplicate entries
                }

                $run = (new Run())
                    ->setDate($rawRace['date'])
                    ->setCity($rawRace['city'])
                    ->setCircuits($rawRace['circuits'])
                    ->setDistances($rawRace['distances'])
                    ->setOrganization($organization)
                    ->setAge($rawRace['age'])
                    ->setSubscribe($rawRace['subscriber'] ?? null)
                    ->setResult($rawRace['result'] ?? null);

                $this->em->persist($run);
            }
            $races[] = $run;
        }
        $this->em->flush();
        return $races;
    }
}