<?php


namespace App\Service\RaceParser;


use League\Pipeline\StageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParseParticipants implements StageInterface
{
    private const BASE_URL = 'https://www.uvponline.nl/uvponlineF/inschrijven_overzicht/';

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
                dd($rawRace);
            }
            unset($rawRace);
        }
        return $rawRaces;
    }

    private function getCircuits(int $id)
    {
        $url = sprintf('%s%s', self::BASE_URL, $id);
        $this->logger->info(sprintf('Circuits [%s] Getting all circuits', $url));
        $crawler = new Crawler(file_get_contents($url));
        $crawler = $crawler->filter('div#ingeschreven_cats li');

        $circuits = $crawler->each(function (Crawler $node) {
            $link = $node->filter('span.cat_beschrijving_step1 a');
            return [
                'name' => $link->text(),
                'participants' => $this->getParticipants($link->attr('href'))
            ];
        });
        $this->logger->info(sprintf('Circuits [%s] Found %d circuits', $url, count($circuits)));
        return $circuits;
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

            return [
                'first' => $this->sanitize($rawText[1]),
                'last' => $this->sanitize($rawText[0]),
                'city' => $this->sanitize($rawText[2]),
                'gender' => $this->sanitize($rawText[3]),
            ];
        });
        $this->logger->info(sprintf('Participants [%s] Found %d', $url, count($participants)));
        return $participants;
    }

    private function sanitize($text)
    {
        $new = preg_replace('/\PL/u', '', $text);
        return $new;
    }
}