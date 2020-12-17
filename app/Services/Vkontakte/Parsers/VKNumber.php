<?php

declare(strict_types=1);

namespace App\Services\Vkontakte\Parsers;

class VKNumber
{
    /** @var string $html */
    private $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function parse(): int
    {
        $multiplier = 1;
        if (preg_match('#\dK#i', $this->html)) {
            $multiplier = 1000;
        } elseif (preg_match('#\dM#i', $this->html)) {
            $multiplier = 1000000;
        }

        $count = (float)preg_replace('#([^0-9.KM]+)#i', '', $this->html);

        return (int)($count * $multiplier);
    }
}
