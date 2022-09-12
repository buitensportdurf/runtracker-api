<?php


namespace App\Service\RaceParser;


use App\Entity\Circuit;
use DateTime;
use League\Pipeline\StageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParseRuns implements StageInterface
{
    private const C_DATE = 0;
    private const C_LOCATION = 1;
    private const C_CLASS_L = 2;
    private const C_CLASS_M = 3;
    private const C_CLASS_K = 4;
    private const C_CLASS_J = 5;
    private const C_CLASS_B = 6;
    private const C_QUALIFIER = 7;
    private const C_DISTANCE = 8;
    private const C_AGE = 9;
    private const C_ORGANIZER = 10;
    private const C_SUBSCRIBE = 11;
    private const C_RESULT = 12;

    private $baseUrl = "https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema";

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ParseRuns constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function url(string $year): string
    {
        return sprintf('%s/%s', $this->baseUrl, $year);
    }

    public function __invoke($payload)
    {
        $allRuns = [];
        foreach ($payload as $year) {
            $url = $this->url($year);

            $this->logger->info(sprintf('Runs [%s] Start parsing runs overview', $url));
            $crawler = new Crawler(file_get_contents($url));

            $crawler = $crawler->filter('table.wedstrijdagenda tr')->nextAll();
            $runs = $crawler->each(function (Crawler $node) {
                $rawText = $node->filter('td')->each(function (Crawler $node) {
                    return $node->text();
                });

                // Replace estafette by 100km
                $rawDistances = str_replace(['Estaffete', 'Estafette'], ['100 km', '100 km'], $rawText[self::C_DISTANCE]);
                // Get all distances
                // Make sure there's no garbage (such as "ook op 9 en 10 juli") in the distance field
                if (!str_contains($rawDistances, 'km')) {
                    $this->logger->error(sprintf('Run has no proper distance defined: "%s"', $rawDistances));
                    return null;
                }
                preg_match_all('/(\d+(?:,5)?)/', $rawDistances, $distances);
                // Parse the minimum age
                preg_match('/(?:vanaf )?(\d+) jaar/', $rawText[self::C_AGE], $age);

                // Try to parse the UVP subscribe ID which might not be gettable if not announced
                $url = $node->filter('td.inschrijflink a');
                $subscribeId = -1;
                $openDate = null;
                if ($url->count() > 0) {
                    preg_match('/\/uvponlineF\/inschrijven\/(\d+)/', $url->attr('href'), $idMatches);
                    preg_match('/(\d{2}-\d{2}-\d{4})(\d{2}:\d{2})?/', $url->text(), $openDateMatches);
                    $subscribeId = intval($idMatches[1]);

                    // Parse the opening date
                    if (count($openDateMatches) == 2) {
                        $openDate = DateTime::createFromFormat('d-m-Y', $openDateMatches[1]);
                        $openDate->setTime(12, 0); // If no time is specified, assume it opens at 12
                    } elseif (count($openDateMatches) == 3) {
                        $openDate = DateTime::createFromFormat('d-m-YH:s', $openDateMatches[1] . $openDateMatches[2]);
                    }
                }

                $run = [
                    'date' => DateTime::createFromFormat('d-m-Y', $rawText[self::C_DATE]),
                    'competitions' => [
                        Circuit::TYPE_COMPETITION_LONG => strlen($rawText[self::C_CLASS_L]) === 1,
                        Circuit::TYPE_COMPETITION_MEDIUM => strlen($rawText[self::C_CLASS_M]) === 1,
                        Circuit::TYPE_COMPETITION_SHORT => strlen($rawText[self::C_CLASS_K]) === 1,
                        Circuit::TYPE_COMPETITION_YOUTH => strlen($rawText[self::C_CLASS_J]) === 1,
                        Circuit::TYPE_COMPETITION_BASE => strlen($rawText[self::C_CLASS_B]) === 1,
                    ],
                    'qualifier' => strlen($rawText[self::C_QUALIFIER]) > 10,
                    'distances' => $distances[0] ?: ['100'],
                    'age' => intval($age[1]),
                    'org' => [
                        'name' => $rawText[self::C_ORGANIZER],
                        'url' => $node->filter('td#wedstrijdlink a')->attr('href'),
                    ],
                    'subscribeId' => $subscribeId,
                    'openDate' => $openDate,
                ];
                // flip the circuit types if true
                $competitions = [];
                foreach ($run['competitions'] as $type => $set) {
                    if ($set) {
                        $competitions[] = $type;
                    }
                }
                $run['competitions'] = $competitions ?: [];

                // parse the city
                $city = $rawText[self::C_LOCATION];
                if (str_contains($city, 'AFGELAST')) {
                    $run['cancelled'] = true;
                    $city = str_replace('AFGELAST ', '', $city);
                } else {
                    $run['cancelled'] = false;
                }
                $city = str_replace(['(za)', '(zo)'], ['', ''], $city);
                // Remove championships, maybe parse later?
                $city = str_replace(['ONK LSR', 'ONK MSR', 'ONK KSR', 'ONK JSR', 'ONK Koppel', 'NSK', 'BK'], ['', '', '', '', '', '', ''], $city);
                $run['city'] = trim($city);

                // parse the subscribe url
//            $race = $node->filter('td.inschrijflink a');
//            if ($race->count() > 0) {
//                $run['subscribe'] = 'https://www.uvponline.nl' . $race->attr('href');
//            }
                // parse result
                $result = $node->filter('td.uitslaglink_definitief a');
                if ($result->count() > 0) {
                    $run['result'] = $result->attr('href');
                }

                return $run;
            });

            $runs = array_filter($runs);
            $this->logger->info(sprintf('Runs [%s] Found %d runs', $url, count($runs)));

            $allRuns = array_merge($allRuns, $runs);
        }
        return $allRuns;
    }
}