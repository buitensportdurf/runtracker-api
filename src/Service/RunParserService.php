<?php


namespace App\Service;


use Symfony\Component\DomCrawler\Crawler;

class RunParserService
{
    private $url = "https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/2020";

    public function getRuns()
    {
        $crawler = new Crawler(file_get_contents($this->url));

        $crawler = $crawler->filter('table.wedstrijdagenda tr')->nextAll();
        return $crawler->each(function (Crawler $node, $i) {
            $raw = $node->filter('td')->each(function (Crawler $node, $i) {
                return $node->text();
            });

            $race = $node->filter('td.inschrijflink a');
            $data = [
                'date' => $raw[0],
                'city' => $raw[1],
                'circuits' => [
                    'L' => strlen($raw[2]) === 1,
                    'M' => strlen($raw[3]) === 1,
                    'K' => strlen($raw[4]) === 1,
                    'J' => strlen($raw[5]) === 1,
                ],
                'qualifier' => $raw[6],
                'distance' => $raw[7],
                'age' => $raw[8],
                'org' => [
                    'name' => $raw[9],
                    'url' => $node->filter('td#wedstrijdlink a')->attr('href'),
                ],
                'race' => [
                    'text' => $raw[10],
                    'url' => $race->count() ? $race->attr('href') : null,
                ]
            ];

            return $data;
        });
    }


}