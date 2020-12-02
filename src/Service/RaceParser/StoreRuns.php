<?php


namespace App\Service\RaceParser;


use App\Entity\Circuit;
use App\Entity\Organization;
use App\Entity\Run;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Pipeline\StageInterface;
use Psr\Log\LoggerInterface;

class StoreRuns implements StageInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StoreRaces constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function __invoke($rawRaces)
    {
        $this->logger->notice('Start storing the data');
        foreach ($rawRaces as $rawRace) {
            $runRepo = $this->em->getRepository(Run::class);
            $organizationRepo = $this->em->getRepository(Organization::class);
            $circuitRepo = $this->em->getRepository(Circuit::class);
            $userRepo = $this->em->getRepository(User::class);

            // Get the organization or create if not already exists
            if (!($organization = $organizationRepo->findOneBy(['name' => $rawRace['org']['name']]))) {
                $organization = (new Organization())
                    ->setName($rawRace['org']['name'])
                    ->setWebsite($rawRace['org']['url']);
                $this->logger->info(sprintf('Creating organization %s', $organization));
                $this->em->persist($organization);
                $this->em->flush(); // Flush to make sure we don't get duplicate entries
            }

            // First try to find existing run
            if (!($run = $runRepo->findOneBy(['date' => $rawRace['date'], 'city' => $rawRace['city']]))) {
                // Create the run
                $run = (new Run())
                    ->setDate($rawRace['date'])
                    ->setCity($rawRace['city'])
                    ->setOrganization($organization)
                    ->setAge($rawRace['age'])
                    ->setCancelled($rawRace['cancelled'])
                    ->setSubscribe($rawRace['subscriber'] ?? null)
                    ->setResult($rawRace['result'] ?? null)
                    ->setEnrollId($rawRace['subscribeId']);

                $this->logger->info(sprintf('Storing run %s', $run));
                $this->em->persist($run);
            }


            /**
             * We add the dummy circuits if we have no real circuits parsed and no real circuits attached
             */
            if (empty($rawRace['circuits'])) {
                $this->logger->info('Start storing dummy circuits');
                $competitions = $rawRace['competitions'];
                // Make a dummy for each distance that doesn't exist
                foreach ($rawRace['distances'] as $distance) {
                    $distance = floatval(str_replace(',', '.', $distance));
                    $rawName = sprintf('dummy_%.1fkm', $distance);
                    // We find by distance so that we don't overwrite existing ones
                    if (!($circuit = $circuitRepo->findOneBy(['distance' => $distance, 'run' => $run]))) {
                        $circuit = (new Circuit())
                            ->setRawName($rawName)
                            ->setDistance($distance)
                            ->setPrice(0)
                            ->setDummy(true)
                            ->setType('dummy')
                            ->setCompetitionType(array_pop($competitions))
                            ->setRun($run);

                        $this->logger->debug(sprintf('Creating dummy %s for run %s', $circuit, $run));

                        $this->em->persist($circuit);
                        $this->em->flush();
                    }
                }
            } else {
                // Create the real circuits and participants
                $this->logger->info('Start storing real circuits and users');
                foreach ($rawRace['circuits'] as $rawCircuit) {
                    if (!($circuit = $circuitRepo->findOneBy(['rawName' => $rawCircuit['raw_name']]))) {
                        $circuit = (new Circuit())
                            ->setRawName($rawCircuit['raw_name'])
                            ->setDistance($rawCircuit['distance'])
                            ->setPrice($rawCircuit['price'])
                            ->setGroupSize($rawCircuit['group_size'])
                            ->setType($rawCircuit['type'])
                            ->setDescription('')
                            ->setMinAge($rawCircuit['min_age'])
                            ->setMaxAge($rawCircuit['max_age'])
                            ->setUserCapacity($rawCircuit['participants_max'])
                            ->setDummy(false)
                            ->setRun($run);

                        // Delete the dummy for this distance if it exists
                        if (($dummy = $circuitRepo->findOneBy(['distance' => $rawCircuit['distance'], 'run' => $run, 'dummy' => true]))) {
                            $this->logger->debug(sprintf('Delete dummy %s for run %s', $dummy, $run));
                            $this->em->remove($dummy);
                        }
                    }
                    // Updatable values
                    $circuit->setUserCount($rawCircuit['participants_current'])
                        ->setPoints($rawCircuit['points'])
                        ->setCompetitionType($rawCircuit['competition_type']);

                    $this->em->persist($circuit);
                    $this->em->flush();

                    foreach ($rawCircuit['participants'] as $participant) {
                        $username = strtolower($participant['first'] . ' ' . $participant['middle'] . ' ' . $participant['last']);
                        $username = str_replace(' ', '_', $username);
                        // Find the user by first/last name
                        if (!($user = $userRepo->findOneBy([
                            'username' => $username,
                        ]))) {
                            $user = (new User())
                                ->setCity($participant['city'])
                                ->setFirstName($participant['first'])
                                ->setMiddleName($participant['middle'])
                                ->setLastName($participant['last'])
                                ->setGender($participant['gender'])
                                ->setUsername($username)
                                ->setPassword('x'); // hashed, so impossible to use

                            $this->em->persist($user);
                            $this->em->flush();
                        }
                        $circuit->addUser($user);
                    }

                    $this->em->persist($circuit);
                }
            }
        }
        $this->em->flush();
        return $rawRaces;
    }
}