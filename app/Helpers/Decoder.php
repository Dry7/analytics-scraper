<?php

declare(strict_types=1);

namespace App\Helpers;

trait Decoder
{
    protected function decode(string $text): string
    {
        return iconv('cp1251', 'utf-8', $text);
    }
}
