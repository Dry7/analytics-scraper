<?php

namespace App\Types;

class Network
{
    public const VKONTAKTE = 1;
    public const ODNOKLASSNIKI = 2;
    public const FACEBOOK = 3;
    public const MAIL = 4;

    private const CODES = [
        self::VKONTAKTE => 'vk',
        self::ODNOKLASSNIKI => 'ok',
        self::FACEBOOK => 'fb',
        self::MAIL => 'mail',
    ];

    /**
     * @param int $network
     *
     * @return string|null
     */
    public static function getCode(int $network): ?string
    {
        return self::CODES[$network] ?? null;
    }

    public static function getVkontakteCode(): string
    {
        return self::getCode(self::VKONTAKTE);
    }

    public static function getOdnoklassnikiCode(): string
    {
        return self::getCode(self::ODNOKLASSNIKI);
    }

    public static function getFacebookCode(): string
    {
        return self::getCode(self::FACEBOOK);
    }

    public static function getMailCode(): string
    {
        return self::getCode(self::MAIL);
    }
}