<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Services\Html\VKService;
use App\Types\Type;
use Carbon\Carbon;

class VKTest extends \TestCase
{
    /** @var VKService */
    private $service;

    public function setUp()
    {
        $this->createApplication();

        $this->service = app(VKService::class);
    }

    public function tearDown()
    {
        Carbon::setTestNow();
        $this->sleep(1);
    }

    function membersDataProvider()
    {
        return [
            ['avtopodbor48',  1600],
            ['meduzaproject', 520000],
            ['record',        4200000],
        ];
    }

    /**
     * @test
     *
     * @dataProvider membersDataProvider
     *
     * @param string $slug
     * @param int $minMembers
     *
     * @throws
     */
    public function members(string $slug, int $minMembers)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertGreaterThan($minMembers, $data['members']);
    }

    function sourceIdDataProvider()
    {
        return [
            ['europaplus',      19043],
            ['tvcomedy',        491],
            ['free_audiobooks', 73476],
            ['no_kia',          1622],
            ['baraholkaua',     444351],
            ['wannasex',        33099],
            ['best_ex_club',    9645],
        ];
    }

    /**
     * @test
     *
     * @dataProvider sourceIdDataProvider
     *
     * @param string $slug
     * @param int $sourceId
     *
     * @throws
     */
    public function sourceId(string $slug, int $sourceId)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($sourceId, $data['source_id']);
    }

    function typeIdDataProvider()
    {
        return [
            ['virosli_v_90', Type::GROUP],
            ['igromania',    Type::GROUP],
            ['kinopoisk',    Type::PUBLIC],
            ['dfm',          Type::PUBLIC],
            ['event115',     Type::EVENT],
            ['event333605',  Type::EVENT],
        ];
    }

    /**
     * @test
     *
     * @dataProvider typeIdDataProvider
     *
     * @param string $slug
     * @param int $typeId
     *
     * @throws
     */
    public function typeId(string $slug, int $typeId)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($typeId, $data['type_id']);
    }

    function titleDataProvider()
    {
        return [
            ['bestdemotivators', 'Демотиваторы'],
            ['vestifuture',      'Новости из будущего'],
            ['best_girl_ukr',    '&#9829;&#9829;&#9829;За девушек из Украины&#9829;&#9829;&#9829;'],
            ['art_erotika',      'АРТ ЭРОТИКА'],
            ['originalsclub',    '&#593;did&#593;s / / / Originals'],
            ['club155209',       'БРЮНЕТКИ ПРАВЯТ МИРОМ&#33;&#33;&#33;'],
            ['mfoterminal',      'МКК &quot;Терминал Финанс&quot;'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider titleDataProvider
     *
     * @param string $slug
     * @param string $title
     *
     * @throws
     */
    public function title(string $slug, string $title)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($title, $data['title']);
    }

    function urlDataProvider()
    {
        return [
            ['club45776', 'https://vk.com/maximfadeev',    'maximfadeev'],
            ['club69915', 'https://vk.com/golubie_bereti', 'golubie_bereti'],
            ['event382',  'https://vk.com/event382',       'event382'],
            ['public826', 'https://vk.com/musichunters',   'musichunters'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider urlDataProvider
     *
     * @param string $slug
     * @param string $expectedUrl
     * @param string $expectedSlug
     *
     * @throws
     */
    public function url(string $slug, string $expectedUrl, string $expectedSlug)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expectedUrl, $data['url']);
        $this->assertEquals($expectedSlug, $data['slug']);
    }

    function isVerifiedDataProvider()
    {
        return [
            ['club147174',  true],
            ['club217557',  true],
            ['event214772', false],
            ['event120108', false],
            ['club71659',   false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isVerifiedDataProvider
     *
     * @param string $slug
     * @param bool $expected
     *
     * @throws
     */
    public function isVerified(string $slug, bool $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expected, $data['is_verified']);
    }

    function avatarDataProvider()
    {
        return [
            ['stop_dieting', 'https://pp.userapi.com/c625728/v625728289/43718/zqWS6ImmZEY.jpg?ava=1'],
            ['public3305',   'https://pp.userapi.com/c636022/v636022766/38e6c/n9Ky0x0Fnck.jpg?ava=1'],
            ['club15122',    'https://pp.userapi.com/c622829/v622829224/41c26/L9mAlqL9V7o.jpg?ava=1'],
            ['mfoterminal',  'https://pp.userapi.com/c626231/v626231635/2ad95/V_jaE6i4_IQ.jpg?ava=1'],
            ['event343945',  'https://pp.userapi.com/c1245/g343945/a_fa4e90e1.jpg?ava=1'],
            ['event524',     'https://vk.com/images/community_100.png'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider avatarDataProvider
     *
     * @param string $slug
     * @param string $expected
     *
     * @throws
     */
    public function avatar(string $slug, string $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expected, $data['avatar']);
    }

    function postsDataProvider()
    {
        return [
            ['club102189',       null],
            ['infinityconcert',  9000],
            ['nba_club1222',     70000],
            ['in_russia',        56000],
            ['concertsinmoscow', 290],
        ];
    }

    /**
     * @test
     *
     * @dataProvider postsDataProvider
     *
     * @param string $slug
     * @param int|null $minMembers
     *
     * @throws
     */
    public function posts(string $slug, ?int $minMembers)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        if (is_null($minMembers)) {
            $this->assertNull($data['posts']);
        } else {
            $this->assertGreaterThan($minMembers, $data['posts']);
        }
    }

    function isClosedDataProvider()
    {
        return [
            ['club101867',  true],
            ['club101922',  true],
            ['club101810',  false],
            ['event101821', false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isClosedDataProvider
     *
     * @param string $slug
     * @param bool $expected
     *
     * @throws
     */
    public function isClosed(string $slug, bool $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expected, $data['is_closed']);
    }

    function isAdultDataProvider()
    {
        return [
            ['club103709',  true],
            ['vsevnews',  true],
            ['beforeny',  true],
            ['club150276', false],
            ['tgomel', false],
            ['event136744',  false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isAdultDataProvider
     *
     * @param string $slug
     * @param bool $expected
     *
     * @throws
     */
    public function isAdult(string $slug, bool $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expected, $data['is_adult']);
    }

    function isBannedDataProvider()
    {
        return [
            ['club15',    true],
            ['club32',    true],
            ['club40',    true],
            ['club26',    false],
            ['vnorilske', false],
            ['club33',    false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isBannedDataProvider
     *
     * @param string $slug
     * @param bool $expected
     *
     * @throws
     */
    public function isBanned(string $slug, bool $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        if ($expected) {
            $this->assertNull($data);
        } else {
            $this->assertFalse($data['is_banned']);
        }
    }

    function openedAtDataProvider()
    {
        return [
            ['club1292',  '1809-01-01 00:00:00'],
            ['public226', null],
            ['club13',    null],
            ['event368',  null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider openedAtDataProvider
     *
     * @param string $slug
     * @param string|null $expected
     *
     * @throws
     */
    public function openedAt(string $slug, ?string $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expected, $data['opened_at']);
    }

    function lastPostAtDataProvider()
    {
        return [
            ['avtoradio',       (new Carbon())->subDays(2)],
            ['best_psychology', (new Carbon())->subDays(2)],
            ['nevberega_sept',  new Carbon('2018-05-22 20:31:00')],
            ['club525200',      new Carbon('2015-01-25 00:00:00')],
            ['club526072',      null],
            ['club525975',      null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider lastPostAtDataProvider
     *
     * @param string $slug
     * @param string|null $expected
     *
     * @throws
     */
    public function lastPostAt(string $slug, ?string $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        if (is_null($expected)) {
            $this->assertNull($data['last_post_at']);
        } else {
            $this->assertGreaterThan($expected, $data['last_post_at']);
        }
    }

    function addressDataProvider()
    {
        return [
            ['team', ['RU', 'RU-SPE', 498817]],
            ['gaz',  ['RU', 'RU-MOW', 524901]],
            ['ababahalamaha.publishers',  ['UA', 'UA-30', 703448]],
            ['bgk_meshkova',  ['BY', 'BY-BR', '629634']],
            ['club525865',  [null, null, null]],
        ];
    }

    /**
     * @test
     *
     * @dataProvider addressDataProvider
     *
     * @param string $slug
     * @param array $expected
     *
     * @throws
     */
    public function address(string $slug, array $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expected, [$data['country_code'], $data['state_code'], $data['city_code']]);
    }

    function eventDatesDataProvider()
    {
        return [
            ['event101',        '2015-10-04 00:00:00', null],
            ['event362748',     '2030-04-13 13:00:00', '2030-04-20 19:00:00'],
            ['englishvacation', '2018-08-27 12:00:00', '2018-08-31 14:00:00'],
            ['club188598',      null,                  null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider eventDatesDataProvider
     *
     * @param string $slug
     * @param string|null $expectedStart
     * @param string|null $expectedEnd
     *
     * @throws
     */
    public function eventDates(string $slug, ?string $expectedStart, ?string $expectedEnd)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertEquals($expectedStart, $data['event_start']);
        $this->assertEquals($expectedEnd,   $data['event_end']);
    }

    public function emptyWallDataProvider()
    {
        return [
            [4319],
            [4467],
            [5660],
        ];
    }

    /**
     * @test
     *
     * @dataProvider emptyWallDataProvider
     *
     * @param int $sourceId
     *
     * @throws
     */
    public function emptyWall(int $sourceId)
    {
        // act
        $data = $this->service->runWall(['source_id' => $sourceId]);

        // assert
        $this->assertEmpty($data);
    }

    public function oldWallDataProvider()
    {
        return [
            [6307, '2016-01-01 00:00:00',
                [
                    ['id' => 2409, 'date' => '2016-02-25 00:00:00', 'likes' => 4, 'shares' => 0, 'views' => 0, 'comments' => 0, 'is_pinned' => false, 'is_ad' => false, 'links' => []],
                    ['id' => 2408, 'date' => '2016-02-24 00:00:00', 'likes' => 0, 'shares' => 0, 'views' => 0, 'comments' => 0, 'is_pinned' => false, 'is_ad' => false, 'links' => []],
                ]
            ],
            [376606, '2016-01-01 00:00:00',
                [
                    ['id' => 1110, 'date' => '2016-10-07 00:00:00', 'likes' => 0,  'shares' => 0, 'views' => 0, 'comments' => 0,  'is_pinned' => false, 'is_ad' => false, 'links' => ['http://run.myviasat.ru/',]],
                    ['id' => 1108, 'date' => '2016-10-03 00:00:00', 'likes' => 10, 'shares' => 0, 'views' => 0, 'comments' => 2,  'is_pinned' => false, 'is_ad' => false, 'links' => []],
                    ['id' => 1106, 'date' => '2016-10-03 00:00:00', 'likes' => 18, 'shares' => 5, 'views' => 0, 'comments' => 0,  'is_pinned' => false, 'is_ad' => false, 'links' => []],
                    ['id' => 1070, 'date' => '2016-09-30 00:00:00', 'likes' => 31, 'shares' => 2, 'views' => 0, 'comments' => 25, 'is_pinned' => false, 'is_ad' => false, 'links' => []],
                    ['id' => 1062, 'date' => '2016-09-30 00:00:00', 'likes' => 49, 'shares' => 3, 'views' => 0, 'comments' => 17, 'is_pinned' => false, 'is_ad' => false, 'links' => []],
                ]
            ],
            [407223, '2012-01-01 00:00:00',
                [
                    ['id' => 15, 'date' => '2012-05-07 00:00:00', 'likes' => 0, 'shares' => 0, 'views' => 0, 'comments' => 0, 'is_pinned' => false, 'is_ad' => false, 'links' => []]
                ]
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider oldWallDataProvider
     *
     * @param int $sourceId
     * @param string $date
     * @param array $expected
     *
     * @throws
     */
    public function oldWall(int $sourceId, string $date, array $expected)
    {
        // arrange
        Carbon::setTestNow($date);

        // act
        $data = $this->service->runWall(['source_id' => $sourceId]);

        // assert
        $this->assertEquals($expected, $data);
    }

    /**
     * @test
     *
     * @param int $sourceId
     *
     * @throws
     */
    public function popularWall(int $sourceId = 108468)
    {
        // act
        $posts = collect($this->service->runWall(['source_id' => $sourceId]));

        $this->assertCount(100, $posts);

        // assert
        $this->assertGreaterThanOrEqual(10, $posts->avg('likes'));
        $this->assertGreaterThanOrEqual(5, $posts->avg('shares'));
        $this->assertGreaterThanOrEqual(2000, $posts->avg('views'));
        $this->assertGreaterThanOrEqual(2, $posts->avg('comments'));
    }

    /**
     * @test
     *
     * @param int $sourceId
     *
     * @throws
     */
    public function hasAdOnWall(int $sourceId = 140813)
    {
        // arrange
        Carbon::setTestNow('2017-01-01');

        // act
        $ads = collect($this->service->runWall(['source_id' => $sourceId]));

        // assert
        $this->assertTrue($ads->isNotEmpty());
    }
}
