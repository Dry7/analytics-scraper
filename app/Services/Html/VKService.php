<?php

declare(strict_types=1);

namespace App\Services\Html;

use App\Helpers\Utils;
use App\Services\CountryService;
use App\Services\Html\Parsers\VKDate;
use App\Types\Type;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class VKService
{
    private const BASE_URL = 'https://vk.com/';

    private const INFO = [
        'links' => 'Ссылки',
        'photos' => 'Фотографии',
        'boards' => 'Обсуждения',
        'audio' => 'Аудиозаписи',
        'video' => 'Видео',
        'market' => 'Товары',
        'members_possible' => 'Возможные участники',
    ];

    /** @var Client */
    private $client;

    /** @var VKDate */
    private $date;

    /** @var array */
    private $clientOptions = [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36'
        ]
    ];

    /** @var CountryService */
    private $countryService;

    /** @var int */
    private $maxWallPages;

    /** @var int */
    private $maxWallDate;

    /** @var int */
    private $wallOffset;

    /**
     * VKService constructor.
     * @param Client $client
     * @param VKDate $date
     * @param CountryService $countryService
     * @param int $maxWallPages
     * @param int $maxWallDate
     * @param int $wallOffset
     */
    public function __construct(
        Client $client,
        VKDate $date,
        CountryService $countryService,
        int $maxWallPages = 5,
        int $maxWallDate = 2,
        int $wallOffset = 20
    )
    {
        $this->client = $client;
        $this->date = $date;

        if (!empty(config('adspy.ips'))) {
            $this->clientOptions['curl'] = [
                CURLOPT_INTERFACE => Utils::randomArrayValue(config('adspy.ips')),
            ];
        }

        $this->countryService = $countryService;
        $this->maxWallPages = $maxWallPages;
        $this->maxWallDate = $maxWallDate;
        $this->wallOffset = $wallOffset;
    }

    /**
     * @param string $slug
     * @param bool $processWall
     *
     * @return array|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function scraper(string $slug, $processWall = true): ?array
    {
        if (!($html = $this->load($slug))) {
            return null;
        }

        $group = $this->parseHTML($html);

        if (is_null($group['source_id']) || is_null($group['members']) || !($group['members'] > 0)) {
            return null;
        }

        if ($processWall && $this->isCheckWall($group)) {
            $group['wall'] = $this->runWall($group);

            if ($group['last_post_at'] instanceof Carbon && !is_string($group['last_post_at'])) {
                $group['last_post_at'] = $group['last_post_at']->toDateTimeString();
            }
        }

        return $group;
    }

    /**
     * @param array $group
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function runWall(array $group)
    {
        $dateFilter = Carbon::now()->subMonths($this->maxWallDate);

        $offset = $page = 0;

        $wall = [];

        do {
            $count = 0;
            foreach ($this->wall($group['source_id'], $offset) as $post) {
                $wall[] = $post;
                $count++;
                if (!$post['is_pinned'] && $post['date'] < $dateFilter) {
                    return $wall;
                }
            }
            if ($count == $this->wallOffset) {
                $offset += $count;
            } else {
                return $wall;
            }
            $page++;
        } while ($page < $this->maxWallPages);

        return $wall;
    }

    /**
     * @param array $group
     * @return bool
     */
    private function isCheckWall(array $group): bool
    {
        return !$group['is_closed']
            && !$group['is_banned']
            && ($group['members'] > 5000)
            && ($group['posts'] > 0 || is_null($group['posts']))
            && !is_null($group['last_post_at'])
            && $group['last_post_at'] > Carbon::now()->subMonths($this->maxWallDate);
    }

    /**
     * @param string $slug
     * @return null|string
     * @throws \Exception
     */
    private function load(string $slug): ?string
    {
        try {
            $response = $this->client->get(self::BASE_URL . $slug, $this->clientOptions)->getBody()->getContents();

            if ($this->isRateLimit($response)) {
                sleep(5);
                throw new \Exception('VK rate limit exceeded');
            }

            return $response;
        } catch (RequestException $exception) {
            return null;
        }
    }

    private function isRateLimit(string $html)
    {
        return preg_match('#Вы попытались загрузить более одной однотипной страницы в секунду.#i', $html);
    }

    /**
     * @param string $html
     * @return array
     *
     * @throws \Exception
     */
    private function parseHTML(string $html): array
    {
        $html = preg_replace('#<span class="num_delim"> </span>#i', '', $html);

        $result = [
            'source_id'    => null,
            'title'        => null,
            'members'      => null,
            'url'          => null,
            'slug'         => null,
            'is_verified'  => null,
            'opened_at'    => null,
            'last_post_at' => null,
            'avatar'       => null,
            'posts'        => null,
            'country_code' => null,
            'state_code'   => null,
            'city_code'    => null,
            'event_start'  => null,
            'event_end'    => null,
            'contacts'     => [],
        ];

        if (preg_match('#<title>(.*)</title>#i', $html, $title)) {
            $result['title'] = str_replace(' | ВКонтакте', '', $this->decode($title[1]));
        }

        if (preg_match('#<em class="pm_counter">(.*)</em>#i', $html, $members)) {
            $em = strpos($members[1], '</em>');
            if ($em !== false) {
                $members[1] = substr($members[1], 0, $em);
            }
            $result['members'] = (int)preg_replace('/[^0-9]*/i', '', $members[1]);
        }

        if (empty($result['members'])
            && preg_match('#page\.showPageMembers\(event,\s*-\d+,\s*\'members\'\)"\s*class="module_header">\s*<div\s*class="header_top\s*clear_fix">\s*<span class="header_label fl_l">[^<]+</span>\s*<span class="header_count fl_l">([^<]+)</span>\s*</div>#i', $html, $members)) {
            $result['members'] = (int)preg_replace('/[^0-9]*/i', '', $members[1]);
        }

        $result['is_verified'] = (bool)preg_match('#<h2\s*class="page_name">[^<]+<a\s*href="\/verify"\s*class="page_verified#i', $html);
        $result['is_closed'] = (bool)!preg_match('#wall_module#i', $html);
        $result['is_adult'] = (bool)preg_match('#"age_disclaimer":true#i', $html);
        $result['is_private'] = (bool)preg_match('#Это частное сообщество. Доступ только по приглашениям администраторов.#i', $html);
        $result['is_banned'] = (bool)(preg_match('#Сообщество заблокировано в связи с возможным нарушением правил сайта.#i', $html)
        || preg_match('#Данный материал заблокирован на территории Российской Федерации#i', $html));

        if (preg_match('#mhi_back">Мероприятие</span>#i', $html)
            || preg_match('#id="event_admin"#i', $html)) {
            $result['type_id'] = Type::EVENT;
        } elseif (preg_match('#mhi_back">Страница</span>#i', $html)
            || preg_match('#public_followers#i', $html)
            || preg_match('#<aside aria-label="Подписчики">#i', $html)) {
            $result['type_id'] = Type::PUBLIC;
        } else {
            $result['type_id'] = Type::GROUP;
        }

        if (preg_match('#<dt>Дата основания:</dt><dd>(.*)</dd>#i', $html, $opened_at)
         || preg_match('#<div class="group_info_row date" title="[^"]+">([^<]*)</div>#i', $html, $opened_at)
         || preg_match('#<div class="group_info_row date" title="[^"]*">\s*<div class="line_value">([^<]*)</div>\s*</div>#i', $html, $opened_at)) {
            $result['opened_at'] = $this->date->parse($this->decode($opened_at[1]))->toDateTimeString();
        }

        if (preg_match('#<dl class="pinfo_row"><dt>Место:</dt><dd><a(?: [^>]*)>([^<]*)</a>#i', $html, $city)
         || preg_match('#<div class="group_info_row address" title="[^"]+"><a href="[^"]+">([^<]*)</a></div>#', $html, $city)
         || preg_match('#class="address_link">([^<]*)</a>#', $html, $city)) {
            foreach ($this->countryService->findCity($this->decode(strip_tags($city[1]))) as $key => $value) {
                $result[$key] = $value;
            }
        }

        if (preg_match('#<dt>Начало:</dt><dd>([^>]*)</dd>#i', $html, $event_start)
         || preg_match('#<div class="group_info_row time" title="[^"]+">([^<]*)</div>#', $html, $event_start)
         || preg_match('#<div class="group_info_row time" title="[^"]+">\s*<div class="line_value">([^<]*)</div>\s*</div>#', $html, $event_start)
         || preg_match('#<div class="group_info_row soon" title="[^"]+">([^<]*)</div>#', $html, $event_start)) {
            $event_start = preg_replace('#(Событие состоялось|Мероприятие состоялось)#i', '', $this->decode($event_start[1]));
            if (strpos($event_start, '&mdash;') !== false) {
                $events = explode('&mdash;', $event_start);
                $result['event_start'] = $this->date->parse(trim($events[0]))->toDateTimeString();
                $result['event_end'] = $this->date->parse(trim($events[1]))->toDateTimeString();
            } else {
                $result['event_start'] = $this->date->parse(trim($event_start))->toDateTimeString();
            }
        }

        if (preg_match('#<dt>Окончание:</dt><dd>([^>]*)</dd>#i', $html, $event_end)) {
            $result['event_end'] = $this->date->parse($event_end[1])->toDateTimeString();
        }

        if (preg_match('#<img src="(.*)" class="pp_img#i', $html, $avatar)
        || preg_match('#<a class="page_cover_image" [^>]*>\s*<img src="([^"]+)"#i', $html, $avatar)
        || preg_match('#<div id="page_avatar" class="page_avatar"><a [^>]*><img class="page_avatar_img" src="([^"]+)"#i', $html, $avatar)
        || preg_match('#<div id="page_avatar" class="page_avatar"><img class="page_avatar_img" src="([^"]+)"#i', $html, $avatar)) {
            $result['avatar'] = $avatar[1];
            if (in_array($result['avatar'], ['/images/community_100.png', '/images/camera_100.png'])) {
                $result['avatar'] = self::BASE_URL . substr($result['avatar'], 1, strlen($result['avatar']));
            }
        }

        if (empty($result['avatar'])) {
            $result['avatar'] = self::BASE_URL . 'images/community_100.png';
        }

        if (preg_match('#<span class="slim_header_label">(.*)</span>#i', $html, $posts)) {
            $result['posts'] = (int)preg_replace('/[^0-9]*/i', '', $posts[1]);
        }

        if (!($result['posts'] > 0) && preg_match('#wall"><h4 class="slim_header clearfix"><span class="slim_header_label">(.*)</span>#i', $html, $posts)) {
            $result['posts'] = (int)preg_replace('/[^0-9]*/i', '', $posts[1]);
        }

        if (!($result['posts'] > 0) && preg_match('#<a name="wall"></a>\s*<h4 class="slim_header">(.*)</h4>#i', $html, $posts)) {
            $result['posts'] = (int)preg_replace('/[^0-9]*/i', '', $posts[1]);
        }

        if (!($result['posts'] > 0) && preg_match('#id="page_wall_count_own" value="(\d+)"#', $html, $posts)) {
            $result['posts'] = (int)$posts[1];
        }

        if (!((int)$result['posts'] > 0)) {
            if (preg_match('#<input type="hidden" id="page_wall_count_own" value="(.*)" />#i', $html, $posts)) {
                $result['posts'] = (int)$posts[1];
            } else {
                $result['posts'] = null;
            }
        }

        if (preg_match('#<a href="\/wall\?act=toggle_subscribe\&owner_id=\-(\d*)&#i', $html, $source_id)) {
            $result['source_id'] = (int)$source_id[1];
        } elseif(preg_match('#page\.showPageMembers\(event, -(\d+), \'members\'\)"#i', $html, $source_id)) {
            $result['source_id'] = (int)$source_id[1];
        }

        if (preg_match('#<link rel="canonical" href="([^"]*)" />#i', $html, $url)) {
            $result['url'] = $url[1];
            $result['slug'] = str_replace(self::BASE_URL, '', $result['url']);
        } elseif (preg_match('#<link rel="alternate" media="only screen and \(max-width: 640px\)" href="([^"]+)" />#', $html, $url)) {
            $result['url'] = preg_replace('#^https://m.vk.com#i', 'https://vk.com', $url[1]);
            $result['slug'] = str_replace(self::BASE_URL, '', $result['url']);
        }

        foreach (self::INFO as $key => $value) {
            if (preg_match('#' . $value . ' <em class="pm_counter">([^<]*)</em>#i', $html, $item)) {
                $result[$key] = (int)$item[1];
            } else {
                $result[$key] = null;
            }
        }

        $result['contacts'] = $this->parseContacts($html);

        foreach ($this->loadWallFromGroup($html) as $key => $val) {
            $result[$key] = $val;
        }

        return $result;
    }

    private function parseContacts(string $html): array
    {
        preg_match_all('#fl_l thumb">\s+<a\s+href="([^"]+)"><img\s+class="cell_img"\s+src="([^"]+)"\s+alt="([^"]*)"#', $html, $images);
        if (empty($images[1])) {
            preg_match_all('#<a\s+href="([^"]+)"\s+class="line_cell\s+clear_fix">\s+<div class="fl_l\s+thumb">\s+<img\s+class="cell_img"\s+src="([^"]+)"\s+alt="([^"]+)"#', $html, $images);
        }

        $contacts = [];

        foreach ($images[1] as $i => $url) {
            $contacts[] = [
                'avatar' => $this->normalizeUrl($images[2][$i]),
                'name' => $this->decode($images[3][$i]),
                'url' => $this->normalizeUrl($url),
            ];
        }

        return $contacts;
    }

    /**
     * @todo check method
     *
     * @param string $html
     * @return array
     */
    private function loadWallFromGroup(string $html)
    {
        $lastPostAt = null;

        if (!preg_match_all('#own[^"]*" data-post-id="-([^"]*)"#i', $html, $ids)) {
            $ids = [1 => []];
        }

        if (!preg_match_all('#<a class="wi_date"(?: [^>]*)>([^<]*)</a>#i', $html, $dates)) {
            if (!preg_match_all('#showWiki\({w:\s*\'wall-(\d+_\d+)\'},\s*false,\s*event\);" ><span class="rel_date[^>]*>([^<]+)</span>#i', $html, $dates)) {
                $dates = [1 => []];
            }
        }

        if (!preg_match_all('#aria-label="(\d+) Нравится"><i class="i_like">#i', $html, $likes)) {
            if (!preg_match_all('#Likes\.showLikes\(this,\s+\'wall-(\d+_\d+)\',\s+{}\)"\s+data-count="(\d+)"#i', $html, $likes)) {
                $likes = [1 => []];
            }
        }

        if (!preg_match_all('#aria-label="(\d+) Поделиться"><i class="i_share">#i', $html, $shares)) {
            if (!preg_match_all('#Likes.showShare\(this,\s+\'wall-(\d+_\d+)\'\);"\s+data-count="(\d+)"#i', $html, $shares)) {
                $shares = [1 => []];
            }
        }

        if (!preg_match_all('#no_views|aria-label="(\d+) (просмотр|просмотра|просмотров)*"><i class="i_views">#i', $html, $views)) {
            if (!preg_match_all('#Likes.updateViews\(\'wall-(\d+_\d+)\'\);">([^<]+)</div>#i', $html, $views)) {
                $views = [1 => []];
            }
        }

        $posts = [];

        if (!(sizeof($ids[1]) > 0)) {
            return [
                'last_post_at' => null,
            ];
        }

        $dates  = $this->mergeCounts($dates[1], $dates[2]);
        $likes  = $this->mergeCounts($likes[1], $likes[2]);
        $shares = $this->mergeCounts($shares[1], $shares[2]);
        $views  = $this->mergeCounts((array)@$views[1], (array)@$views[2]);

        foreach ($ids[1] as $i => $id) {
            $date = $this->date->parse($this->decode($dates[$id]));
            if (is_null($lastPostAt) || $date > $lastPostAt) {
                $lastPostAt = $date;
            }
            $posts[] = [
                'id'     => array_last(explode('_', $id)),
                'date'   => $date->toDateTimeString(),
                'likes'  => $likes[$id] ?? null,
                'shares' => $shares[$id] ?? null,
                'views'  => $this->getNumber($views[1][$i] ?? '0'),
            ];
        }

        return [
            'last_post_at' => $lastPostAt instanceof Carbon ? $lastPostAt->toDateTimeString() : null,
//            'wall'         => $posts,
        ];
    }

    private function mergeCounts(array $keys, array $values): array
    {
        $array = [];

        foreach ($keys as $i => $key) {
            $array[$key] = $values[$i];
        }

        return $array;
    }

    /**
     * @param int $groupId
     * @param int $offset
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function loadWall(int $groupId, int $offset = 0): string
    {
        $response = (string)$this->client->request('GET', self::BASE_URL . 'wall-' . $groupId,
            [
              'headers' => $this->clientOptions['headers'] +
                  [
                  'cookie' => 'remixsid=' . Utils::randomArrayValue(config('scraper.vk_keys')),
              ],
              'query' => [
                'own' => 1,
                'offset' => $offset
              ]
        ])->getBody();

        if ($this->isRateLimit($response)) {
            sleep(5);
            throw new \Exception('VK rate limit exceeded');
        }

        return $response;
    }

    /**
     * @param int $groupId
     * @param int $offset
     * @return \Generator
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function wall(int $groupId, int $offset = 0): \Generator
    {
        $html = $this->loadWall($groupId, $offset);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $posts = $xpath->query('//div[@class="wall_item"]');

        if ($posts->length == 0) {
            $posts = $xpath->query('//div[contains(@class, "_post post")]');
        }

        /** @var \DOMElement $posts */
        foreach ($posts as $post) {
            $video = $this->getPostVideo($xpath, $post);
            $sharedPost = $this->getSharedPost($xpath, $post);
            $hasNextComments = $this->hasNextComments($xpath, $post);

            yield [
                'id' => $this->getPostId($xpath, $post),
                'date' => $this->getPostDate($xpath, $post)->toDateTimeString(),
                'likes' => $this->getCount($xpath, $post, 'like'),
                'shares' => $this->getCount($xpath, $post, 'share'),
                'views' => $this->getPostViews($xpath, $post),
                'has_next_comments' => $hasNextComments,
                'comments' => $this->getCount($xpath, $post, 'comment'),
                'is_pinned' => $this->getPostPinned($xpath, $post),
                'is_ad' => $this->getPostAd($xpath, $post),
                'is_gif' => $this->isPostHasGif($xpath, $post),
                'is_video' => !is_null($video),
                'video_group_id' => (int)$video['group_id'] ?: null,
                'video_id' => (int)$video['video_id'] ?: null,
                'shared_group_id' => (int)$sharedPost['group_id'] ?: null,
                'shared_post_id' => (int)$sharedPost['post_id'] ?: null,
                'links' => $this->getPostLinks($xpath, $post),
            ];
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return int|null
     */
    private function getPostId(\DOMXPath &$xpath, \DOMElement &$post): ?int
    {
        $id = $xpath->query('.//a[contains(@class, "post__anchor")]', $post);

        if (!isset($id[0])) {
            return (int)array_last(explode('_', $post->getAttribute('data-post-id')));
        }

        return (int)array_last(explode('_', $id[0]->getAttribute('name')));
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return Carbon|null
     */
    private function getPostDate(\DOMXPath &$xpath, \DOMElement &$post)
    {
        $date = $xpath->query('.//a[@class="wi_date"]', $post);

        if (!isset($date[0])) {
            $date = $xpath->query('.//span[@class="rel_date"]', $post);
            if (!isset($date[0])) {
                $date = $xpath->query('.//span[@class="rel_date rel_date_needs_update"]', $post);
                if (!isset($date[0])) {
                    return null;
                }
            }
        }

        return $this->date->parse($date[0]->textContent);
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @param string $element
     * @return int
     */
    private function getCount(\DOMXPath &$xpath, \DOMElement &$post, string $element): int
    {
        $count = $xpath->query('.//b[@class="v_' . $element . '"]', $post);

        if (!isset($count[0])) {
            $count = $xpath->query('.//div[contains(@class, "feedback_' . $element . '")]', $post);
            if (!isset($count[0])) {
                $count = $xpath->query('.//div[contains(@class, "like_wrap _like_wall-")]//a[contains(@class, "like_btn ' . $element . ' _' . $element . '")]', $post);
                if (!isset($count[0])) {
                    return 0;
                }
                return $this->getNumber($count[0]->getAttribute('data-count'));
            }
        }

        $count = $count[0]->textContent;

        return $this->getNumber($count);
    }

    private function getPostViews(\DOMXPath &$xpath, \DOMElement &$post)
    {
        $count = $xpath->query('.//div[contains(@class, "like_wrap _like_wall-")]//div[contains(@class, "like_views _views")]', $post);

        if (!isset($count[0])) {
            return 0;
        }

        return $this->getNumber($count[0]->textContent);
    }

    private function getNumber(string $count): int
    {
        $multiplier = 1;
        if (preg_match('#\dK#i', $count)) {
            $multiplier = 1000;
        } elseif (preg_match('#\dM#i', $count)) {
            $multiplier = 1000000;
        }

        $count = (float)preg_replace('#([^0-9.KM]+)#i', '', $count);

        return (int)($count * $multiplier);
    }

    public function hasNextComments(\DOMXPath &$xpath, \DOMElement &$post): bool
    {
        return $xpath->query('.//a[contains(@class, "replies_next_main")]', $post)->length > 0;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return int
     */
    private function getComments(\DOMXPath &$xpath, \DOMElement &$post): int
    {
        try {
            $comments = $xpath->query('.//a[@class="wr_header"]', $post);
            if (!isset($comments[0])) {
                return $xpath->query('.//div[contains(@class, "reply_wrap")]', $post)->length;
            }
            return (int)array_last(explode('/', $comments[0]->getAttribute('offs')));
        } catch (\Exception $exception) {
            return 0;
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return bool
     */
    private function getPostPinned(\DOMXPath &$xpath, \DOMElement &$post): bool
    {
        return in_array('post_fixed', explode(' ', $post->getAttribute('class')));
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return bool
     */
    private function getPostAd(\DOMXPath &$xpath, \DOMElement &$post): bool
    {
        return $xpath->query('.//div[@class="wall_marked_as_ads"]', $post)->length > 0;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return array
     */
    private function getPostLinks(\DOMXPath &$xpath, \DOMElement &$post): array
    {
        $urls = [];
        try {
            $links = $xpath->query('.//div[@class="wall_text"]//a[contains(@href, "/away.php?to=")]', $post);
            if ($links->length > 0) {
                foreach ($links as $link) {
                    $urls[] = $this->getLinkFromQueryString($link->getAttribute('href'));
                }
            }
            return collect($urls)->unique()->filter(function($url) { return strlen($url) <= 500; })->toArray();
        } catch (\Exception $exception) {
            return $urls;
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return array|null
     */
    private function getPostVideo(\DOMXPath &$xpath, \DOMElement &$post): ?array
    {
        $video = $xpath->query('.//div[contains(@class, "wall_text")]//div[contains(@class, "post_video_desc")]', $post)->item(0);

        if ($video) {
            $html = $video->ownerDocument->saveHTML($video);

            if (preg_match('#return\s+showVideo\(\'-*(\d+)_(\d+)\'#i', $html, $video)) {
                return [
                    'group_id' => $video[1],
                    'video_id' => $video[2],
                ];
            }
        }

        return null;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return bool
     */
    private function isPostHasGif(\DOMXPath &$xpath, \DOMElement &$post): bool
    {
        return $xpath->query('.//div[@class="page_gif_play_icon"]', $post)->length > 0;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $post
     * @return array|null
     */
    private function getSharedPost(\DOMXPath &$xpath, \DOMElement &$post): ?array
    {
        $link = $xpath->query('.//a[contains(@class, "copy_author")]', $post);

        if ($link->length > 0) {
            $sharedPost = explode('_', $link->item(0)->getAttribute('data-post-id'));

            return count($sharedPost) === 2 ? [
                'group_id' => abs($sharedPost[0]),
                'post_id' => abs($sharedPost[1]),
            ] : null;
        }

        return null;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getLinkFromQueryString(string $url): string
    {
        parse_str(parse_url($url)['query'], $result);

        return $this->decode($result['to']);
    }

    /**
     * @param string $text
     * @return string
     */
    private function decode(string $text): string
    {
        return iconv('cp1251', 'utf-8', $text);
    }

    private function normalizeUrl(string $image): string
    {
        return !preg_match('#^http#', $image) ? 'https://vk.com' . $image : $image;
    }
}