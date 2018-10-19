<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Html\VKService;
use App\Services\ScraperService;
use App\Types\Network;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class UpdateGroupJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    private $networkId;

    /** @var string */
    private $url;

    /** @var int */
    public $tries = 3;

    /** @var int  */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param int $networkId
     * @param string $url
     *
     * @return void
     */
    public function __construct(int $networkId, string $url)
    {
        $this->networkId = $networkId;
        $this->url = $url;
    }

    /**
     * @param VKService $service
     * @param ScraperService $scraper
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(VKService $service, ScraperService $scraper)
    {
        $data = $service->scraper($this->url);

        if (!is_null($data)) {
            $scraper->sendGroup(Network::getCode($this->networkId), $data);
        }
    }
}
