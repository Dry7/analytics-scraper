<?php

declare(strict_types=1);

namespace App\Tests\Services\Html\Parsers;

use App\Services\Html\Parsers\VKDate;
use Carbon\Carbon;

class VKDateTest extends \TestCase
{
    /** @var VKDate */
    private $service;

    public function setUp()
    {
        $this->service = app(VKDate::class);
        Carbon::setTestNow('2018-06-02 12:00:00');
    }

    public function tearDown()
    {
        Carbon::setTestNow();
    }

    public function parseDataProvider()
    {
        return [
            ['минуту назад', '2018-06-02 11:59:00'],
            ['две минуты назад', '2018-06-02 11:58:00'],
            ['26 минут назад', '2018-06-02 11:34:00'],
            ['31 минуту назад', '2018-06-02 11:29:00'],
            ['36 минут назад', '2018-06-02 11:24:00'],
            ['32 минуты назад', '2018-06-02 11:28:00'],
            ['38 минут назад', '2018-06-02 11:22:00'],
            ['только что', '2018-06-02 12:00:00'],
            ['час назад', '2018-06-02 11:00:00'],
            ['два часа назад', '2018-06-02 10:00:00'],
            ['три часа назад', '2018-06-02 09:00:00'],
            ['четыре часа назад', '2018-06-02 08:00:00'],
            ['сегодня в 9:07', '2018-06-02 09:07:00'],
            ['сегодня в 10:09', '2018-06-02 10:09:00'],
            ['сегодня в 14:03', '2018-06-02 14:03:00'],
            ['вчера в 4:16', '2018-06-01 04:16:00'],
            ['вчера в 20:09', '2018-06-01 20:09:00'],
            ['29 мая в 15:39', '2018-05-29 15:39:00'],
            ['23 мая в 20:33', '2018-05-23 20:33:00'],
            ['10 мая в 23:53', '2018-05-10 23:53:00'],
            ['8 мая в 0:23', '2018-05-08 00:23:00'],
            ['12 апр в 0:11', '2018-04-12 00:11:00'],
            ['6 апр в 23:31', '2018-04-06 23:31:00'],
            ['4 апр в 13:19', '2018-04-04 13:19:00'],
            ['30 мар в 21:27', '2018-03-30 21:27:00'],
            ['26 мар в 13:37', '2018-03-26 13:37:00'],
            ['12 мар в 22:17', '2018-03-12 22:17:00'],
            ['31 мая в 23:59', '2018-05-31 23:59:00'],
            ['31 мая в 6:38', '2018-05-31 06:38:00'],
            ['24 мая в 23:10', '2018-05-24 23:10:00'],
            ['5 апр в 0:19', '2018-04-05 00:19:00'],
            ['14 мар в 9:31', '2018-03-14 09:31:00'],
            ['23 фев в 23:11', '2018-02-23 23:11:00'],
            ['7 фев в 11:29', '2018-02-07 11:29:00'],
            ['1 янв 2016', '2016-01-01 00:00:00'],
            ['28 янв 2016', '2016-01-28 00:00:00'],
            ['7 фев 2016', '2016-02-07 00:00:00'],
            ['14 фев 2016', '2016-02-14 00:00:00'],
            ['7 мар 2016', '2016-03-07 00:00:00'],
            ['15 мар 2016', '2016-03-15 00:00:00'],
            ['9 апр 2016', '2016-04-09 00:00:00'],
            ['13 апр 2016', '2016-04-13 00:00:00'],
            ['3 мая 2016', '2016-05-03 00:00:00'],
            ['27 мая 2016', '2016-05-27 00:00:00'],
            ['6 июн 2017', '2017-06-06 00:00:00'],
            ['10 июн 2017', '2017-06-10 00:00:00'],
            ['2 июл 2015', '2015-07-02 00:00:00'],
            ['28 июл 2015', '2015-07-28 00:00:00'],
            ['2 авг 2015', '2015-08-02 00:00:00'],
            ['28 авг 2015', '2015-08-28 00:00:00'],
            ['7 сен 2017', '2017-09-07 00:00:00'],
            ['30 сен 2016', '2016-09-30 00:00:00'],
            ['5 окт 2017', '2017-10-05 00:00:00'],
            ['30 окт 2017', '2017-10-30 00:00:00'],
            ['5 ноя 2015', '2015-11-05 00:00:00'],
            ['24 ноя 2015', '2015-11-24 00:00:00'],
            ['1 дек 2015', '2015-12-01 00:00:00'],
            ['16 дек 2015', '2015-12-16 00:00:00'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider parseDataProvider
     *
     * @param string $date
     * @param string $expected
     */
    public function parse(string $date, string $expected)
    {
        // act
        $result = $this->service->parse($date);

        // assert
        $this->assertEquals($expected, $result->toDateTimeString());
    }
}
