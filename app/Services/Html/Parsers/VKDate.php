<?php

declare(strict_types=1);

namespace App\Services\Html\Parsers;

use Carbon\Carbon;

class VKDate
{
    /**
     * @param string $date
     * @return Carbon
     */
    public function parse(string $date)
    {
        switch ($date) {
            case 'только что':
                return Carbon::now();

            case 'минуту назад':
            case 'одну минуту назад':
                return Carbon::now()->subMinute();

            case 'две минуты назад':
                return Carbon::now()->subMinutes(2);

            case 'три минуты назад':
                return Carbon::now()->subMinutes(3);

            case 'четыре минуты назад':
                return Carbon::now()->subMinutes(4);

            case 'пять минут назад':
                return Carbon::now()->subMinutes(5);

            case 'шесть минут назад':
                return Carbon::now()->subMinutes(6);

            case 'семь минут назад':
                return Carbon::now()->subMinutes(7);

            case 'восемь минут назад':
                return Carbon::now()->subMinutes(8);

            case 'девять минут назад':
                return Carbon::now()->subMinutes(9);

            case 'десять минут назад':
                return Carbon::now()->subMinutes(10);

            case 'час назад':
                return Carbon::now()->subHour()->second(0);

            case 'два часа назад':
                return Carbon::now()->subHours(2)->second(0);

            case 'три часа назад':
                return Carbon::now()->subHours(3)->second(0);

            case 'четыре часа назад':
                return Carbon::now()->subHours(4)->second(0);

            case 'пять часов назад':
                return Carbon::now()->subHours(5)->second(0);
        }

        if (preg_match('#\d (минут|минуты|минуту) назад#iu', $date)) {
            return Carbon::now()->subMinutes((int)$date);
        }

        if (preg_match('#\d (секунд|секунду|секунды) назад#iu', $date)) {
            return Carbon::now()->subSeconds((int)$date);
        }

        if (preg_match('#\d год#iu', $date)) {
            return Carbon::createFromDate((int)$date, 1, 1)->setTime(0, 0, 0);
        }

        foreach ([
                     'Январь' => 1, 'Февраль' => 2, 'Март' => 3, 'Апрель' => 4, 'Май' => 5, 'Июнь' => 6, 'Июль' => 7,
                     'Август' => 8, 'Сентябрь' => 9, 'Октябрь' => 10, 'Ноябрь' => 11, 'Декабрь' => 12
                 ] as $value => $id) {
            if (preg_match('#' . $value . ' (\d*)#i', $date, $year)) {
                return Carbon::createFromDate($year[1], $id, 1)->setTime(0, 0, 0);
            }
        }

        $months = [
            'января' => 1,
            'янв' => 1,
            'февраля' => 2,
            'фев' => 2,
            'марта' => 3,
            'мар' => 3,
            'апреля' => 4,
            'апр' => 4,
            'мая' => 5,
            'июня' => 6,
            'июн' => 6,
            'июля' => 7,
            'июл' => 7,
            'августа' => 8,
            'авг' => 8,
            'сентября' => 9,
            'сен' => 9,
            'октября' => 10,
            'окт' => 10,
            'ноября' => 11,
            'ноя' => 11,
            'декабря' => 12,
            'дек' => 12,
        ];

        $date = preg_replace('#сегодня в#iu', Carbon::now()->format('d.m.Y'), $date);
        $date = preg_replace('#вчера в#iu', Carbon::now()->subDay()->format('d.m.Y'), $date);
        $date = preg_replace('#Фераль#iu', 'фев', $date);

        $year = $this->getYear($date);

        foreach ($months as $val => $id) {
            $date = preg_replace('#(?: | )' . $val . '(?: | )#iu', '.' . $id . '.' . $year, $date);
        }
        if (!preg_match('#\d{1,2}:\d{1,2}#', $date)) {
            $date .= ' 00:00';
        }

        $date = preg_replace('#в#u', '', $date);

        return Carbon::createFromFormat('d.m.Y H:i', $date)->second(0);
    }

    /**
     * @param string $date
     * @return int|string
     */
    private function getYear(string $date)
    {
        for ($year = Carbon::now()->year, $i = 0; $i < 200; $i++) {
            if (preg_match('#' . ($year - $i) . '#i', $date)) {
                return '';
            }
        }
        for ($year = Carbon::now()->year, $i = 0; $i < 200; $i++) {
            if (preg_match('#' . ($year + $i) . '#i', $date)) {
                return '';
            }
        }

        return Carbon::now()->year;
    }
}
