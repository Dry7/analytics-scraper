<?php

namespace App\Services\Html\Parsers;

class VKPost
{
    public function exportHash(string $html): ?string
    {
        if (preg_match('#\{preview:\s+\d+,\s+width:\s+%width%\},\s+\'([^\']+)\'\)",\s+data#i', $html, $hash)) {
            return $hash[1];
        }

        return null;
    }

    public function comments(string $html): ?int
    {
        if (preg_match('#\{"count":(\d+),"offset":\d+,"num":\d+\}#i', $html, $comments)) {
            return $comments[1];
        }

        return null;
    }

    public function hasNextComments(string $html): bool
    {
        return preg_match('#wall\.showNextReplies#i', $html);
    }
}