<?php

namespace App\Services;

use GuzzleHttp\Client;

class ScraperService
{
    /** @var Client */
    private $client;

    /** @var string */
    private $backendHost;

    public function __construct(Client $client, string $backendHost)
    {
        $this->client = $client;
        $this->backendHost = $backendHost;
    }

    public function send(array $group)
    {
        $this->client->post($this->backendHost . '/api/register', [
            'json' => $group,
        ]);
    }
}