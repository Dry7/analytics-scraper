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
            ['stop_dieting', '#https://[^.]+\.userapi\.com/c625728/v625728289/43718/zqWS6ImmZEY\.jpg\?ava=1#i'],
            ['public3305',   '#https://[^.]+\.userapi\.com/c636022/v636022766/38e6c/n9Ky0x0Fnck\.jpg\?ava=1#i'],
            ['club15122',    '#https://[^.]+\.userapi\.com/c622829/v622829224/41c26/L9mAlqL9V7o\.jpg\?ava=1#i'],
            ['mfoterminal',  '#https://[^.]+\.userapi\.com/c626231/v626231635/2ad95/V_jaE6i4_IQ\.jpg\?ava=1#i'],
            ['event343945',  '#https://[^.]+\.userapi\.com/c1245/g343945/a_fa4e90e1\.jpg\?ava=1#i'],
            ['event524',     '#https://vk\.com/images/community_100\.png#i'],
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
        $this->assertRegExp($expected, $data['avatar']);
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
            ['beforeny',  true],
            ['vsevnews',  false],
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
            ['club34',    false],
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
            ['public29908', '2007-03-18 00:00:00'],
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
            ['bgkmeshkova',  ['BY', 'BY-BR', '629634']],
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
                    ['id' => 2409, 'date' => '2016-02-25 00:00:00', 'likes' => 4, 'shares' => 0, 'views' => 0, 'has_next_comments' => false, 'comments' => 0, 'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null],
                    ['id' => 2408, 'date' => '2016-02-24 00:00:00', 'likes' => 0, 'shares' => 0, 'views' => 0, 'has_next_comments' => false, 'comments' => 0, 'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null],
                ]
            ],
            [376606, '2016-01-01 00:00:00',
                [
                    ['id' => 1110, 'date' => '2016-10-07 00:00:00', 'likes' => 0,  'shares' => 0, 'views' => 0, 'has_next_comments' => false, 'comments' => 0,  'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => ['http://run.myviasat.ru/',], 'shared_group_id' => null, 'shared_post_id' => null],
                    ['id' => 1108, 'date' => '2016-10-03 00:00:00', 'likes' => 10, 'shares' => 0, 'views' => 0, 'has_next_comments' => false, 'comments' => 2,  'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null],
                    ['id' => 1106, 'date' => '2016-10-03 00:00:00', 'likes' => 18, 'shares' => 5, 'views' => 0, 'has_next_comments' => false, 'comments' => 0,  'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null],
                    ['id' => 1070, 'date' => '2016-09-30 00:00:00', 'likes' => 31, 'shares' => 2, 'views' => 0, 'has_next_comments' => true, 'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null],
                    ['id' => 1062, 'date' => '2016-09-30 00:00:00', 'likes' => 49, 'shares' => 3, 'views' => 0, 'has_next_comments' => true, 'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null],
                ]
            ],
            [407223, '2012-01-01 00:00:00',
                [
                    ['id' => 15, 'date' => '2012-05-07 00:00:00', 'likes' => 0, 'shares' => 0, 'views' => 0, 'has_next_comments' => false, 'comments' => 0, 'is_pinned' => false, 'is_ad' => false, 'is_gif' => false, 'is_video' => false, 'video_group_id' => null, 'video_id' => null, 'links' => [], 'shared_group_id' => null, 'shared_post_id' => null]
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
    }

    /**
     * @test
     *
     * @throws
     */
    public function hasAdOnWall()
    {
        // arrange
        Carbon::setTestNow('2017-01-01');

        // act
        $ads = collect($this->service->runWall(['source_id' => 140813]))
            ->filter(function ($item) { return $item['is_ad']; });

        // assert
        $this->assertNotEmpty($ads);
    }

    public static function postVideoDataProvider()
    {
        return [
            [9562, '2016-04-18 00:00:00', 24321187, 456239093],
            [7868, '2016-05-16 00:00:00', 16366828, 456239044],
        ];
    }

    /**
     * @test
     *
     * @dataProvider postVideoDataProvider
     *
     * @param int $sourceId
     * @param string $date
     * @param int $expectedVideoGroupId
     * @param int $expectedVideoId
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postVideo(int $sourceId, string $date, int $expectedVideoGroupId, int $expectedVideoId)
    {
        Carbon::setTestNow($date);

        // act
        $wall = collect($this->service->runWall(['source_id' => $sourceId]))->filter(function ($item) use ($expectedVideoGroupId, $expectedVideoId) {
            return $item['is_video'] === true
                && $item['video_group_id'] === $expectedVideoGroupId
                && $item['video_id'] === $expectedVideoId;
        });

        // assert
        $this->assertNotEmpty($wall);
    }

    public static function postGifDataProvider()
    {
        return [
            [7606, '2017-01-01 00:00:00', 470000],
            [4504, '2017-02-1500:00:00', 170039],
        ];
    }

    /**
     * @test
     *
     * @dataProvider postGifDataProvider
     *
     * @param int $sourceId
     * @param string $date
     * @param int $expectedPostId
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postGif(int $sourceId, string $date, int $expectedPostId)
    {
        Carbon::setTestNow($date);

        // act
        $wall = collect($this->service->runWall(['source_id' => $sourceId]))->filter(function ($item) use ($expectedPostId) {
            return $item['id'] === $expectedPostId
                && $item['is_gif'] === true;
        });

        // assert
        $this->assertNotEmpty($wall);
    }

    /**
     * @test
     *
     * @dataProvider contactsDataProvider
     *
     * @param string $slug
     * @param array $expected
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function contacts(string $slug, array $expected)
    {
        // act
        $data = $this->service->scraper($slug, false);

        // assert
        $this->assertSame($expected, $data['contacts']);
    }

    public function contactsDataProvider()
    {
        return [
            [
                'tvcomedy',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c840235/v840235623/7e7c8/68GUOjopCdM.jpg?ava=1',
                        'name' => 'Стас Кбржданский',
                        'url' => 'https://vk.com/stascomedy',
                    ],
                ],
            ],
            [
                'astromo',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c9848/u9443299/e_51b153b2.jpg?ava=1',
                        'name' => 'Karina Sviridova',
                        'url' => 'https://vk.com/karinasviridova',
                    ],
                ],
            ],
            [
                'club24',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c71/u49186/e_e231de63.jpg?ava=1',
                        'name' => 'Олег Княгинин',
                        'url' => 'https://vk.com/id49186',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c301707/v301707365/4ee3/u2EFpalnY9U.jpg?ava=1',
                        'name' => 'Станислав Вахитов',
                        'url' => 'https://vk.com/svahitov',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c637130/v637130219/8121/QShqWj4bw7c.jpg?ava=1',
                        'name' => 'Майкл Сенин',
                        'url' => 'https://vk.com/mikesenin',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c536/u00039/e_c1035f8a.jpg?ava=1',
                        'name' => 'David Mirelli',
                        'url' => 'https://vk.com/id39',
                    ],
                    [
                        'avatar' => 'https://sun1-13.userapi.com/xdrhF3pojHjw8HdbKIZ5GwFczhOap6Hzqbp5SQ/qJsXjsoM0Uk.jpg?ava=1',
                        'name' => 'Ренат Садеков',
                        'url' => 'https://vk.com/rin4ik0',
                    ],
                ],
            ],
            [
                'public12648877',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c316123/u29647021/e_28a2cd17.jpg?ava=1',
                        'name' => 'Владимир Щербаков',
                        'url' => 'https://vk.com/smm_consulting',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c630427/v630427305/2cb65/ryogPWokO0o.jpg?ava=1',
                        'name' => 'Vladimir Belikov',
                        'url' => 'https://vk.com/id80725305',
                    ],
                ]
            ]
        ];
    }
}
