<?php

namespace App\Console\Commands;

use App\Services\Html\VKService;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'analytics:test';

    public function handle(VKService $service)
    {
        $data = $service->scraper('meduzaproject');

        print_r($data);
        $this->info('Test');
    }
}