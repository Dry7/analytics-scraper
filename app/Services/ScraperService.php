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

    public function sendPostExportHash(string $networkCode, array $data): void
    {
        $this->send($this->backendHost . '/api/'  . $networkCode . '/posts/export-hash', $networkCode, $data);
    }

    public function sendPostComments(string $networkCode, array $data): void
    {
        $this->send($this->backendHost . '/api/'  . $networkCode . '/posts/comments', $networkCode, $data);
    }

    public function sendGroup(string $networkCode, array $data): void
    {
        $this->send($this->backendHost . '/api/'  . $networkCode . '/register', $networkCode, $data);
    }

    private function send(string $url, string $networkCode, array $data)
    {
        $this->logger->log($networkCode, $data);

        $this->client->post($url, [
            'headers' => [
                'X-API-KEY' => config('scraper.api_key'),
            ],
            'json' => $data,
        ]);
    }
}