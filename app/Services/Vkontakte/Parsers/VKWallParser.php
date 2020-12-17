<?php

declare(strict_types=1);

namespace App\Services\Vkontakte\Parsers;

use App\Helpers\Decoder;
use App\Services\Html\Parsers\VKDate;

class VKWallParser
{
    use Decoder;

    /** @var string */
    private $html;

    /** @var array */
    private $posts = [];

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function parse(): self
    {
        $lastPostAt = null;

        if (!preg_match_all('#data-post-id="-([^"]*)" onclick="wall\.postClick\(#i', $this->html, $ids)) {
            $ids = [1 => []];
        }

        if (!preg_match_all('#<a class="wi_date"(?: [^>]*)>([^<]*)</a>#i', $this->html, $dates) && !preg_match_all('#showWiki\({w:\s*\'wall-(\d+_\d+)\'},\s*false,\s*event\);" ><span class="rel_date[^>]*>([^<]+)</span>#i', $this->html, $dates)) {
            $dates = [1 => []];
        }

        if (!preg_match_all('#aria-label="(\d+) Нравится"><i class="i_like">#iu', $this->html, $likes) && !preg_match_all('#Likes\.showLikes\(this,\s+\'wall-(\d+_\d+)\',\s+{}\)"\s+data-count="(\d+)"#i', $this->html, $likes)) {
            $likes = [1 => []];
        }

        if (!preg_match_all('#aria-label="(\d+) Поделиться"><i class="i_share">#iu', $this->html, $shares) && !preg_match_all('#Likes.showShare\(this,\s+\'wall-(\d+_\d+)\'\);"\s+data-count="(\d+)"#i', $this->html, $shares)) {
            $shares = [1 => []];
        }

        if (!preg_match_all('#no_views|aria-label="(\d+) (просмотр|просмотра|просмотров)*"><i class="i_views">#iu', $this->html, $views) && !preg_match_all('#Likes.updateViews\(\'wall-(\d+_\d+)\'\);">([^<]+)</div>#i', $this->html, $views)) {
            $views = [1 => []];
        }

        $this->posts = [];

        if (count($ids[1]) === 0) {
            return $this;
        }

        $dates  = $this->mergeCounts($dates[1], $dates[2]);
        $likes  = $this->mergeCounts($likes[1], $likes[2]);
        $shares = $this->mergeCounts($shares[1], $shares[2]);
        $views  = $this->mergeCounts((array)@$views[1], (array)@$views[2]);

        $dateParser = new VKDate();

        foreach ($ids[1] as $i => $id) {
            $date = $dateParser->parse($this->decode($dates[$id]));
            if ($date > $lastPostAt || is_null($lastPostAt)) {
                $lastPostAt = $date;
            }
            $this->posts[] = [
                'id'     => array_last(explode('_', $id)),
                'date'   => $date->toDateTimeString(),
                'likes'  => $likes[$id] ?? null,
                'shares' => $shares[$id] ?? null,
                'views'  => (new VKNumber($views[1][$i] ?? '0'))->parse(),
            ];
        }

        return $this;
    }

    public function getPosts(): array
    {
        return $this->posts;
    }

    public function getLastPostAt(): ?string
    {
        $lastPostAt = null;

        foreach ($this->posts as $post) {
            if ($post['date'] > $lastPostAt || is_null($lastPostAt)) {
                $lastPostAt = $post['date'];
            }
        }

        return $lastPostAt;
    }

    private function mergeCounts(array $keys, array $values): array
    {
        $array = [];

        foreach ($keys as $i => $key) {
            $array[$key] = $values[$i];
        }

        return $array;
    }
}
