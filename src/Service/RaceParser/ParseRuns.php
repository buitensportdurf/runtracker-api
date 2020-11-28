<?php


namespace App\Service\RaceParser;


use App\Entity\Circuit;
use http\Url;
use League\Pipeline\StageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParseRuns implements StageInterface
{
    private $url = "https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/2020";

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ParseRuns constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($payload)
    {
        $this->logger->info(sprintf('Runs [%s] Start parsing runs overview', $this->url));
        $crawler = new Crawler(file_get_contents($this->url));

        $crawler = $crawler->filter('table.wedstrijdagenda tr')->nextAll();
        $runs = $crawler->each(function (Crawler $node) {
            $rawText = $node->filter('td')->each(function (Crawler $node) {
                return $node->text();
            });

            // Replace estafette by 100km
            $rawDistances = str_replace('Estafette suvivalrun', '100 km', $rawText[7]);
            // Get all distances
            preg_match_all('/(\d+(?:,5)?)/', $rawDistances, $distances);
            // Parse the minimum age
            preg_match('/(?:vanaf )?(\d+) jaar/', $rawText[8], $age);

            // Try to parse the UVP subscribe ID which might not be gettable if not announced
            $url = $node->filter('td.inschrijflink a');
            $subscribeId = -1;
            if ($url->count() > 0) {
                preg_match('/\/uvponlineF\/inschrijven\/(\d+)/', $url->attr('href'), $idMatches);
                $subscribeId = intval($idMatches[1]);
            }

            $run = [
                'date' => \DateTime::createFromFormat('d-m-Y', $rawText[0]),
                'competitions' => [
                    Circuit::TYPE_COMPETITION_LONG => strlen($rawText[2]) === 1,
                    Circuit::TYPE_COMPETITION_MEDIUM => strlen($rawText[3]) === 1,
                    Circuit::TYPE_COMPETITION_SHORT => strlen($rawText[4]) === 1,
                    Circuit::TYPE_COMPETITION_YOUTH => strlen($rawText[5]) === 1,
                ],
                'qualifier' => strlen($rawText[6]) > 10,
                'distances' => $distances[0] ?: ['100'],
                'age' => intval($age[1]),
                'org' => [
                    'name' => $rawText[9],
                    'url' => $node->filter('td#wedstrijdlink a')->attr('href'),
                ],
                'subscribeId' => $subscribeId,
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
            $city = $rawText[1];
            if (strpos($city, 'AFGELAST') !== false) {
                $run['cancelled'] = true;
                $city = str_replace('AFGELAST ', '', $city);
            } else {
                $run['cancelled'] = false;
            }
            $city = str_replace(['(za)', '(zo)'], ['', ''], $city);
            // Remove championships, maybe parse later?
            $city = str_replace([' ONK LSR', ' ONK MSR', ' ONK KSR', ' ONK JSR', ' BK'], ['', '', '', '', ''], $city);
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
        $this->logger->info(sprintf('Runs [%s] Found %d runs', $this->url, count($runs)));
        return $runs;
    }
}