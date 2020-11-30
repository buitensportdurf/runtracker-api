<?php


namespace App\Service\RaceParser;


use League\Pipeline\StageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParseParticipants implements StageInterface
{
    private const PARTICIPANTS_URL = 'https://www.uvponline.nl/uvponlineF/inschrijven_overzicht/';
    private const CIRCUIT_NAME_URL = 'https://www.uvponline.nl/uvponlineF/inschrijven/';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($rawRaces)
    {
        $this->logger->info('Start parsing circuits and participants');
        foreach ($rawRaces as &$rawRace) {
            $id = $rawRace['subscribeId'];
            if ($id !== -1) {
                $rawRace['circuits'] = $this->getCircuits($id);
            } else {
                $rawRace['circuits'] = [];
            }
            unset($rawRace);
        }
        return $rawRaces;
    }

    private function getCircuits(int $id)
    {
        // Parse the circuit data
        $circuitsP = $this->getCircuitsParticipants($id);
        $circuitsD = $this->getCircuitsData($id);
        $circuits = [];
        foreach ($circuitsP as $key => $circuit) {
            $circuit += $circuitsD[$key];
            $circuits[] = $circuit;
        }

        $this->logger->info(sprintf('Circuits [%d] Found %d circuits', $id, count($circuits)));
        return $circuits;
    }

    private function getCircuitsParticipants($id)
    {
        $url = sprintf('%s%s', self::PARTICIPANTS_URL, $id);
        $this->logger->info(sprintf('Circuits [%s] Getting all circuits for participants', $url));
        $crawler = new Crawler(file_get_contents($url));
        $crawler = $crawler->filter('div#ingeschreven_cats li');

        // Parse the participants
        return $crawler->each(function (Crawler $node) {
            $link = $node->filter('span.cat_beschrijving_step1 a');
            return [
                'participants' => $this->getParticipants($link->attr('href'))
            ];
        });
    }

    private function getCircuitsData($id)
    {
        $url = sprintf('%s%s', self::CIRCUIT_NAME_URL, $id);
        $this->logger->info(sprintf('Circuits [%s] Getting all circuits for data', $url));
        $crawler = new Crawler(file_get_contents($url));
        $crawler = $crawler->filter('div#categorieen li');

        // Parse the data
        return $crawler->each(function (Crawler $node) {
            $circuit = [];
            $fullness = $node->filter('span.stat_box_values td')->each(function (Crawler $node) {
                return $node->text();
            });
            $circuit['participants_current'] = intval($fullness[0]);
            $circuit['participants_max'] = intval($fullness[2]);

            $description = substr(strtolower($node->filter('span.cat_beschrijving_step1')->text()), 2);
            $circuit['raw_name'] = $description;
            // Get the distance
            preg_match('/([\d]) ?(?:km)/', $description, $distances);
            if (count($distances) > 1) {
                $circuit['distance'] = intval($distances[1]);
            } else {
                $circuit['distance'] = 100;
            }

            // Get the price
            preg_match('/€ ?(\d+\.\d{2})/', $description, $prices);
            $circuit['price'] = floatval($prices[1]);

            // Get the group size
            if ($this->containsOneOfWords($description, ['koppel'])) {
                $circuit['group_size'] = 2;
            } elseif ($this->containsOneOfWords($description, ['groep'])) {
                // todo: parse '(max) {n} personen/deelnemers'
                $circuit['group_size'] = 5;
            } else {
                // default to individual
                $circuit['group_size'] = 1;
            }

            // Get the type
            if ($type = $this->findOneOfWords($description, ['recreatief', 'begeleid', 'wedstrijd'])) {
                $circuit['type'] = $type;
            } else {
                $circuit['type'] = 'unknown';
            }

            // Todo: parse
            $circuit['min_age'] = 0;
            $circuit['max_age'] = 100;
            $circuit['points'] = 0;

            return $circuit;
        });
    }

    private function containsOneOfWords(string $text, array $words)
    {
        return preg_match(sprintf('/(%s)/', implode('|', $words)), $text) === 1;
    }

    private function findOneOfWords(string $text, array $words)
    {
        if (preg_match(sprintf('/(%s)/', implode('|', $words)), $text, $matches) === 1) {
            return $matches[1];
        }
        return false;
    }

    private function getParticipants(string $url)
    {
        $this->logger->info(sprintf('Participants [%s] Getting all', $url));
        $crawler = new Crawler(file_get_contents($url));
        $crawler = $crawler->filter('table.overzicht tr');
        $solo = $crawler->first()->filter('td')->first()->text() === 'Achternaam';
        $crawler = $crawler->nextAll();
        $participants = $crawler->each(function (Crawler $node) use ($solo) {
            $rawText = $node->filter('td')->each(function (Crawler $node) {
                return $node->text();
            });
            // If this is a group race, the first column has the team name
            if (!$solo) {
                $teamName = array_shift($rawText);
            }

            $middleLastName = $this->splitMiddleLast($this->sanitize($rawText[0]));

            return [
                'first' => $this->sanitize($rawText[1]),
                'middle' => $middleLastName['middle'],
                'last' => $middleLastName['last'],
                'city' => $this->sanitize($rawText[2]),
                'gender' => $this->sanitize($rawText[3]),
            ];
        });
        $this->logger->info(sprintf('Participants [%s] Found %d', $url, count($participants)));
        return $participants;
    }

    private function sanitize($text)
    {
        return trim(preg_replace('/[^\pL ]/u', '', $text));
    }

    private function splitMiddleLast($name)
    {
        $middleParts = ['van', 'de', 'den', 'der'];
        $segments = explode(' ', $name);
        $middleNames = [];
        // First check if the name start with one of the middle parts
        foreach ($segments as $segment) {
            if (in_array(strtolower($segment), $middleParts)) {
                $middleNames[] = $segment;
            } else {
                if (!empty($middleNames)) {
                    return [
                        'middle' => implode(' ', $middleNames),
                        'last' => implode(' ', array_diff($segments, $middleNames)),
                    ];
                }
                break;
            }
        }
        // Reverse and check from the end
        foreach (array_reverse($segments) as $segment) {
            if (in_array(strtolower($segment), $middleParts)) {
                $middleNames[] = $segment;
            } else {
                if (!empty($middleNames)) {
                    return [
                        'middle' => implode(' ', array_reverse($middleNames)),
                        'last' => implode(' ', array_diff($segments, $middleNames)),
                    ];
                }
                break;
            }
        }
        return [
            'middle' => null,
            'last' => $name,
        ];
    }
}