<?php


namespace App\Service\RaceParser;


use League\Pipeline\StageInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParseRuns implements StageInterface
{
    private $url = "https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/2020";

    public function __invoke($payload)
    {
        $crawler = new Crawler(file_get_contents($this->url));

        $crawler = $crawler->filter('table.wedstrijdagenda tr')->nextAll();
        return $crawler->each(function (Crawler $node) {
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
            if($url->count() > 0){
                preg_match('/\/uvponlineF\/inschrijven\/(\d+)/', $url->attr('href'), $idMatches);
                $subscribeId = intval($idMatches[1]);
            }

            $data = [
                'date' => \DateTime::createFromFormat('d-m-Y', $rawText[0]),
                'circuits' => [
                    'long' => strlen($rawText[2]) === 1,
                    'medium' => strlen($rawText[3]) === 1,
                    'short' => strlen($rawText[4]) === 1,
                    'youth' => strlen($rawText[5]) === 1,
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
            $circuits = [];
            foreach ($data['circuits'] as $type => $set) {
                if ($set) {
                    $circuits[] = $type;
                }
            }
            $data['circuits'] = $circuits ?: ['none'];

            // parse the city
            $city = $rawText[1];
            if (strpos($city, 'AFGELAST') !== false) {
                $data['cancelled'] = true;
                $city = str_replace('AFGELAST ', '', $city);
            }
            $city = str_replace(['(za)', '(zo)'], ['', ''], $city);
            // Remove championships, maybe parse later?
            $city = str_replace([' ONK LSR', ' ONK MSR', ' ONK KSR', ' ONK JSR', ' BK'], ['', '', '', '', ''], $city);
            $data['city'] = trim($city);

            // parse the subscribe url
//            $race = $node->filter('td.inschrijflink a');
//            if ($race->count() > 0) {
//                $data['subscribe'] = 'https://www.uvponline.nl' . $race->attr('href');
//            }
            // parse result
            $result = $node->filter('td.uitslaglink_definitief a');
            if ($result->count() > 0) {
                $data['result'] = $result->attr('href');
            }

            return $data;
        });
    }
}