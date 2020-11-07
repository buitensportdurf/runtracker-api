<?php


namespace App\Service\RaceParser;


use League\Pipeline\StageInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Component\DomCrawler\Crawler;

class ParseParticipants implements StageInterface
{
    private const BASE_URL = 'https://www.uvponline.nl/uvponlineF/inschrijven_overzicht/';

    public function __invoke($rawRaces)
    {
        foreach ($rawRaces as $rawRace) {
            $id = $rawRace['subscribeId'];
            if ($id !== -1) {
                $categories = $this->getCategories($id);
            }
        }
    }

    private function getCategories($id)
    {
        $crawler = new Crawler(file_get_contents(sprintf('%s/%s', self::BASE_URL, $id)));
        $crawler = $crawler->filter('div#ingeschreven_cats li');

        $data = $crawler->each(function (Crawler $node) {
            $url = $node->filter('span.cat_beschrijving_step1 a')->attr('href');
            return $this->getParticipants($url);
        });
        dd($data);
    }

    private function getParticipants($url)
    {
        $crawler = new Crawler(file_get_contents($url));
        $crawler = $crawler->filter('table.overzicht tr');
        $solo = $crawler->first()->filter('td')->first()->text() === 'Achternaam';
        $crawler = $crawler->nextAll();
        return $crawler->each(function (Crawler $node) use ($solo) {
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
    }

    private function sanitize($text)
    {
        $new = preg_replace('/\PL/u', '', $text);
        return $new;
    }
}