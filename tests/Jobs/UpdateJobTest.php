<?php

namespace App\Tests\Services;

use App\Jobs\UpdateGroupJob;
use App\Services\Html\VKService;
use App\Services\ScraperService;
use App\Types\Network;

class UpdateJobTest extends \TestCase
{
    public function handleDataProvider()
    {
        return [
            ['meduzaproject', $this->fixture('group.json')],
            ['alexander_volkov_club', null],
        ];
    }

    /**
     * @dataProvider handleDataProvider
     *
     * @param string $groupName
     * @param array|null $data
     *
     * @throws
     */
    public function testHandle(string $groupName, ?array $data)
    {
        $vkServiceSpy = \Mockery::spy(VKService::class)->shouldReceive('scraper')->with($groupName)->andReturn($data)->getMock();
        $scraperServiceSpy = \Mockery::mock(ScraperService::class)->shouldReceive('send')->with('vk', $data)->getMock();

        $job = new UpdateGroupJob(Network::VKONTAKTE, $groupName);

        $job->handle($vkServiceSpy, $scraperServiceSpy);

        $vkServiceSpy->shouldHaveReceived('scraper')->with($groupName)->once();
        if (is_null($data)) {
            $scraperServiceSpy->shouldNotHaveReceived('send');
        } else {
            $scraperServiceSpy->shouldHaveReceived('send')->with('vk', $data)->once();
        }
    }
}
