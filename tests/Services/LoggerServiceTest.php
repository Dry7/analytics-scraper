<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Services\LoggerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LoggerServiceTest extends \TestCase
{
    public function logDataProvider()
    {
        $data = $this->fixture('group.json');

        return [
            [true, 'vk', $data, true],
            [true, 'ok', $data, true],
            [true, 'fb', collect($data)->except('source_id')->toArray()],
            [false, 'mail', $data],
        ];
    }

    /**
     * @test
     *
     * @dataProvider logDataProvider
     *
     * @param bool $enabled
     * @param string $networkCode
     * @param array $data
     * @param bool $fileExists
     */
    public function log(bool $enabled, string $networkCode, array $data, bool $fileExists = false)
    {
        // arrange
        Carbon::setTestNow('2018-05-01 00:00:00');
        Storage::fake('local');

        $service = new LoggerService($enabled);

        $fileName = 'requests/' . $networkCode . '/2018-05-01/' . @$data['source_id'] . '.txt';

        // act
        $service->log($networkCode, $data);

        // assert
        if ($fileExists) {
            Storage::disk('local')->assertExists($fileName);
        } else {
            Storage::disk('local')->assertMissing($fileName);
        }
        Carbon::setTestNow();
    }

    public function testGetRandomID()
    {
        // arrange
        $service = new LoggerService();

        // act
        $result = $service->getRandomID();

        // assert
        $this->assertTrue((bool)preg_match('/^UNKNOWN_\d+$/', $result));
    }

    public function testGetRandomIDUnique()
    {
        // arrange
        $service = new LoggerService();

        // act
        $firstResult = $service->getRandomID();
        $secondResult = $service->getRandomID();

        // assert
        $this->assertNotEquals($firstResult, $secondResult);
    }
}
