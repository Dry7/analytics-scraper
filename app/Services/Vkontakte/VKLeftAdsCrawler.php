<?php

declare(strict_types=1);

namespace App\Services\Vkontakte;

use App\Services\Vkontakte\Parsers\VKAdLeftParser;
use GuzzleHttp\Client;

class VKLeftAdsCrawler
{
    /** @var int */
    private $date;

    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->date = time() * 1000;
        $this->client = $client;
    }

    public function handle()
    {
        $html = $this->parseResponse($this->call());

        dd(
            $this->extractAds($html)
        );
    }

    private function call(): string
    {
        $response = $this->client->get('https://ad.mail.ru/adq/', [
            'query' => [
                'callback' => '__rb' . $this->date,
                'q[]' => '45891?n=ads_left',
                'test_id' => 47,
                'cpm_floor' => 1,
                'vk_id' => 126530044,
                'sign' => '2c17f29d8fe031a2853171caf27c5c8c5b64ee93',
            ]
        ]);
        return $response->getBody()->getContents();
    }

    private function parseResponse(string $html): ?string
    {
        $html = preg_replace(
            "#\)\n?$#",
            '',
            preg_replace("#^__rb$this->date && __rb$this->date\(#", '', $html)
        );

        return json_decode($html)[0]->html ?? null;
    }

    private function extractAds(string $html): array
    {
        preg_match_all('#<div\s+class="trg-b-banner trg-url"\s+id="[^"]+">(.+?)</a>\s*</div>#is', $html, $ads);

        return array_map(static function ($ad) { return new VKAdLeftParser($ad); }, $ads[0]);
    }
}
