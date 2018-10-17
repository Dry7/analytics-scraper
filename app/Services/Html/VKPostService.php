<?php

declare(strict_types=1);

namespace App\Services\Html;

use App\Helpers\Utils;
use GuzzleHttp\Client;

class VKPostService
{
    private const BASE_URL = 'https://vk.com/';

    /** @var Client */
    private $client;

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
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        if (!empty(config('adspy.ips'))) {
            $this->clientOptions['curl'] = [
                CURLOPT_INTERFACE => Utils::randomArrayValue(config('adspy.ips')),
            ];
        }
    }

    public function exportHash(int $wallId, int $postId): ?string
    {
        $response = $this->client->post(self::BASE_URL . 'like.php', [
            'headers' => array_merge($this->clientOptions['headers'], [
                'cookie' => 'remixsid=1e3209ad2fcfa3727ee801d6f2074de78b53e0a2704f509360a07',
            ]),
            'form_params' => [
                'act' => 'publish_box',
                'al' => '1',
                'object' => "wall-{$wallId}_{$postId}",
            ],
        ])->getBody()->getContents();

        if (preg_match('#\{preview:\s+\d+,\s+width:\s+%width%\},\s+\'([^\']+)\'\)",\s+data#i', $response, $hash)) {
            return $hash[1];
        }

        return null;
    }
}