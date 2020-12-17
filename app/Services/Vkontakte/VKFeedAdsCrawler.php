<?php

declare(strict_types=1);

namespace App\Services\Vkontakte;

use App\Helpers\Utils;
use App\Services\Vkontakte\Parsers\VKWallParser;
use GuzzleHttp\Client;

class VKFeedAdsCrawler
{
    /** @var Client */
    private $client;

    /** @var array */
    private $clientOptions = [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36',
        ]
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
        if (!empty(config('scraper.ips'))) {
            $this->clientOptions['curl'] = [
                CURLOPT_INTERFACE => Utils::randomArrayValue(config('scraper.ips')),
            ];
        }
        if (!empty(config('scraper.vk_keys'))) {
            $this->clientOptions['headers'] = [
                'cookie' => 'remixsid=' . Utils::randomArrayValue(config('scraper.vk_keys')),
            ];
        }
    }

    public function handle()
    {
        $html = $this->call();
        dd($html);
        $wall = new VKWallParser($html);

        dd(
            $wall->getPosts()
        );
    }

    private function call(): string
    {
        $response = $this->client->get('https://vk.com/feed', [
            'headers' => $this->clientOptions['headers'],
            'query' => [
                'section' => 'recommended',
            ],
        ]);

        return $response->getBody()->getContents();
    }
}
