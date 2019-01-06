<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Html\VKPostService;
use App\Services\ScraperService;
use App\Types\Network;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePostComments extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    private $networkId;

    /** @var int */
    private $groupId;

    /** @var int */
    private $postId;

    /** @var int */
    public $tries = 3;

    /** @var int  */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param int $networkId
     * @param int $groupId
     * @param int $postId
     *
     * @return void
     */
    public function __construct(int $networkId, int $groupId, int $postId)
    {
        $this->networkId = $networkId;
        $this->groupId = $groupId;
        $this->postId = $postId;
    }

    public function handle(VKPostService $service, ScraperService $scraper)
    {
        $scraper->sendPostComments(Network::getCode($this->networkId), [
            'groupId' => $this->groupId,
            'postId' => $this->postId,
            'comments' => $service->comments($this->groupId, $this->postId),
        ]);
    }
}
