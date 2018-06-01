<?php

namespace App\Helpers;

class Utils
{
    public static function string2null($val)
    {
        return $val == ''  ? null : $val;
    }

    public static function randomArrayValue(array $array)
    {
        return !empty($array) ? $array[array_rand($array)] : null;
    }
}