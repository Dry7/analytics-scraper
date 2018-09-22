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
        $this->assertTrue($ads->isNotEmpty());
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
                'club1959',
                [
                    [
                            'avatar' => 'https://pp.userapi.com/c629111/v629111007/7aa63/YzvIv1CD2Cg.jpg?ava=1',
                            'name' => 'Андрей Резников',
                            'url' => 'https://vk.com/andreireznikov',
                    ],
                    [
                            'avatar' => 'https://pp.userapi.com/c841435/v841435392/76d33/a9YMzaI4NgQ.jpg?ava=1',
                            'name' => 'Анна Резникова',
                            'url' => 'https://vk.com/id136722',
                    ],
                    [
                            'avatar' => 'https://pp.userapi.com/c841529/v841529667/58e79/NseF7iEhWsc.jpg?ava=1',
                            'name' => 'Tanya Gubanova',
                            'url' => 'https://vk.com/tatyanagubanova',
                    ],
                    [
                            'avatar' => 'https://pp.userapi.com/c831309/v831309317/176117/B3Rq2TDACH8.jpg?ava=1',
                            'name' => 'Алексей Оболевич',
                            'url' => 'https://vk.com/muzred_record',
                    ],
                    [
                            'avatar' => 'https://pp.userapi.com/c623830/v623830450/35148/gBmOkF8QJZ8.jpg?ava=1',
                            'name' => 'Егор Поляков',
                            'url' => 'https://vk.com/polyakov',
                    ],
                ]
            ],
            [
                'tvcomedy',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c840235/v840235623/7e7c8/68GUOjopCdM.jpg?ava=1',
                        'name' => 'Стас Кбржданский',
                        'url' => 'https://vk.com/stascomedy',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c840621/v840621644/c8fd/m-z5pcCAXHo.jpg?ava=1',
                        'name' => 'Михаил Рылявский',
                        'url' => 'https://vk.com/mihcomedy',
                    ],
                ],
            ],
            [
                'megafon',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c840537/v840537688/cab6/TTOb6JL25GY.jpg?ava=1',
                        'name' => 'Юлия Мегафон',
                        'url' => 'https://vk.com/megafon_cf_help',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c639920/v639920334/491f7/I1N5OYNcBbg.jpg?ava=1',
                        'name' => 'Сергей Мегафон',
                        'url' => 'https://vk.com/id175509986',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c834101/v834101483/1c8bb/wyeQTDNwLDI.jpg?ava=1',
                        'name' => 'Елена Фадеева',
                        'url' => 'https://vk.com/megafonsibir',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c837220/v837220247/4f281/vVni1jj06PA.jpg?ava=1',
                        'name' => 'Мария Мегафон',
                        'url' => 'https://vk.com/megafonvolga_help',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c639224/v639224985/42051/hu-c5Z9suSM.jpg?ava=1',
                        'name' => 'Олег &amp;#922;олесников',
                        'url' => 'https://vk.com/megafonszhelp',
                    ],
                ]
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
                        'avatar' => 'https://pp.userapi.com/c836335/v836335129/7c711/a2ILVbRlKFM.jpg?ava=1',
                        'name' => 'Евгений Прохоров',
                        'url' => 'https://vk.com/id96063866',
                    ],
                ],
            ],
            [
                'dnbclub',
                [
                    [
                        'avatar' => 'https://pp.userapi.com/c9508/v9508112/915/FtIs0YQ8d5E.jpg?ava=1',
                        'name' => 'Павел Барыгин',
                        'url' => 'https://vk.com/id53112',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c846417/v846417842/3d5ed/TkQ-nUfLk7E.jpg?ava=1',
                        'name' => 'Аня Савченко',
                        'url' => 'https://vk.com/id25025',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c847016/v847016948/12206/SpQRh16wVDY.jpg?ava=1',
                        'name' => 'Илья Земсков',
                        'url' => 'https://vk.com/mistwist',
                    ],
                    [
                        'avatar' => 'https://pp.userapi.com/c845323/v845323538/b8896/Rq2W8lr_L4Q.jpg?ava=1',
                        'name' => 'Ольга Орлова',
                        'url' => 'https://vk.com/orlovadnb',
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
