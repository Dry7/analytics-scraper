<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class ScraperService
{
    /** @var Client */
    private $client;

    /** @var LoggerService */
    private $logger;

    /** @var string */
    private $backendHost;

    public function __construct(string $backendHost, Client $client, LoggerService $logger)
    {
        $this->backendHost = $backendHost;
        $this->client = $client;
        $this->logger = $logger;
    }

    public function send(string $networkCode, array $group)
    {
        $this->logger->log($networkCode, $group);

        $this->client->post($this->backendHost . '/api/'  . $networkCode . '/register', [
            'headers' => [
                'X-API-KEY' => config('scraper.api_key'),
            ],
            'json' => $group,
        ]);
    }
}