<?php

namespace App\Helpers;

class Utils
{
    public static function string2null($val)
    {
        return $val == ''  ? null : $val;
    }
}