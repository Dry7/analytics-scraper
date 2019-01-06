<?php

declare(strict_types=1);

namespace App\Services\Html;

use App\Helpers\Utils;
use App\Services\Html\Parsers\VKPost;
use GuzzleHttp\Client;

class VKPostService
{
    private const BASE_URL = 'https://vk.com/';

    /** @var Client */
    private $client;

    /** @var VKPost */
    private $parser;

    /** @var array */
    private $clientOptions = [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36'
        ]
    ];

    /**
     * VKPostService constructor.
     *
     * @param Client $client
     * @param VKPost $parser
     */
    public function __construct(Client $client, VKPost $parser)
    {
        $this->client = $client;
        $this->parser = $parser;

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

    public function exportHash(int $wallId, int $postId): ?string
    {
        $response = $this->client->post(self::BASE_URL . 'like.php', [
            'headers' => $this->clientOptions['headers'],
            'form_params' => [
                'act' => 'publish_box',
                'al' => '1',
                'object' => "wall-{$wallId}_{$postId}",
            ],
        ])->getBody()->getContents();

        sleep(1);

        return $this->parser->exportHash($response);
    }

    public function comments(int $wallId, int $postId): int
    {
        $response = $this->client->post(self::BASE_URL . 'al_wall.php', [
            'form_params' => [
                'act' => 'get_post_replies',
                'al' => 1,
                'count' => 20,
                'item_id' => $postId,
                'offset' => 1000000,
                'owner_id' => -$wallId,
            ],
        ])->getBody()->getContents();

        return $this->parser->comments($response);
    }
}