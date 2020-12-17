<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Html\VKPostService;
use App\Services\Html\VKService;
use App\Services\ScraperService;
use App\Services\Vkontakte\VKFeedAdsCrawler;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'analytics:test';

    public function handle(VKService $service, VKPostService $postService, ScraperService $scraperService)
    {
        dd(
            app(VKFeedAdsCrawler::class)->handle()
        );
//        $hash = $postService->exportHash(272, 5519676);
//        dd($hash);
//        echo $postService->exportHash(5277, 717319);
        Carbon::setTestNow('2016-01-01 00:00:00');
        $group = $service->scraper('best_girl_ukr');
        dd($group);

//        print_r($postService->comments(337, 358705));
//        var_export(collect($data['wall'])->filter(function ($post) { return $post['id'] === 40563; }));
//
//        $scraperService->sendGroup(Network::getVkontakteCode(), $data);
        $this->info('Test');
    }
}
