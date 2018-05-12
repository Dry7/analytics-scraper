<?php

namespace App\Console\Commands;

use App\Services\Html\VKService;
use App\Services\ScraperService;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'analytics:test';

    public function handle(VKService $service, ScraperService $scraperService)
    {
        echo "\nconfig('analytics.backend_host') - " . config('analytics.backend_host') . "\n";
        $data = $service->scraper('meduzaproject');

        print_r($data);

        $scraperService->send($data);
        $this->info('Test');
    }
}