<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Html\VKService;
use App\Services\ScraperService;
use App\Types\Network;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'analytics:test';

    public function handle(VKService $service, ScraperService $scraperService)
    {
        Carbon::setTestNow('2011-01-01 00:00:00');
        $data = $service->scraper('club76982440');

//        var_export($data);

        $scraperService->send(Network::getVkontakteCode(), $data);
        $this->info('Test');
    }
}