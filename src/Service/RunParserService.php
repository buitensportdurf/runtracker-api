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

            preg_match_all('/(\d+(?:,5)?)/', $raw[7], $distances);
            preg_match('/(?:vanaf )?(\d+) jaar/', $raw[8], $age);

            $data = [
                'date' => $raw[0],
                'city' => $raw[1],
                'circuits' => [
                    'long' => strlen($raw[2]) === 1,
                    'medium' => strlen($raw[3]) === 1,
                    'short' => strlen($raw[4]) === 1,
                    'youth' => strlen($raw[5]) === 1,
                ],
                'qualifier' => strlen($raw[6]) > 10,
                'distances' => $distances[0],
                'age' => intval($age[1]),
                'org' => [
                    'name' => $raw[9],
                    'url' => $node->filter('td#wedstrijdlink a')->attr('href'),
                ],
            ];

            $race = $node->filter('td.inschrijflink a');
            if($race->count() > 0) {
                $data['subscribe'] = 'https://www.uvponline.nl' . $race->attr('href');
            }

            $result = $node->filter('td.uitslaglink_definitief a');
            if($result->count() > 0) {
                $data['result'] = $result->attr('href');
            }

            return $data;
        });
    }


}