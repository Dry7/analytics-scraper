<?php

namespace App\Console\Commands;

use App\Services\Html\VKService;
use App\Services\ScraperService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'analytics:test';

    public function handle(VKService $service, ScraperService $scraperService)
    {
        Carbon::setTestNow('2011-01-01 00:00:00');
        $data = $service->runWall(['source_id' => '76982440']);

        var_export($data);
exit();
        $scraperService->send($data);
        $this->info('Test');
    }
}