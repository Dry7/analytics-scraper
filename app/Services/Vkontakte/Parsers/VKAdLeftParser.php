<?php

declare(strict_types=1);

namespace App\Services\Vkontakte\Parsers;

class VKAdLeftParser
{
    /** @var string */
    private $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function getId(): string
    {
        if (preg_match('#<div class="trg-b-banner trg-url" id="b(\d+)_\d+">#', $this->html, $id)) {
            return $id[1];
        }
    }

    public function getUrl(): string
    {
        if (preg_match('#<a\s+rel="noopener"\s+class=trg-b-all-in-link\s+href="([^"]+)"[^>]*>#', $this->html, $url)) {
            return $url[1];
        }
    }

    public function getImage(): string
    {
        if (preg_match('#<img\s*class="trg-b-img-vk[^"]*"\s+src="([^"]+)"[^>]*/>#', $this->html, $image)) {
            return $image[1];
        }
    }

    public function getHeader(): string
    {
        if (preg_match('#<div\s+class="trg-b-header">(.+?)</div>#i', $this->html, $header)) {
            return $header[1];
        }
    }

    public function getDomen(): string
    {
        if (preg_match('#<div\s+class="?trg-b-domen"?>(.+?)</div>#is', $this->html, $domen)) {
            return $domen[1];
        }
    }

    public function getText(): string
    {
        if (preg_match('#<div\s+class="?trg-b-text"?>(.+?)</div>#is', $this->html, $text)) {
            return $text[1];
        }
    }

    public function getDisclaimer(): ?string
    {
        if (preg_match('#<div\s+class="?trg-b-disclaimer"?>(.+?)</div>#is', $this->html, $disclaimer)) {
            return trim($disclaimer[1]);
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'url' => $this->getUrl(),
            'image' => $this->getImage(),
            'domen' => $this->getDomen(),
            'text' => $this->getText(),
            'disclaimer' => $this->getDisclaimer(),
            'html' => $this->html,
        ];
    }
}
