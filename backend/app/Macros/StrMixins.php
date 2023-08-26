<?php

namespace App\Macros;

/**
 * Class StrMixins
 * @package App\Macros
 */
class StrMixins
{
    public function withoutNumbers()
    {
        return function ($str) {
            return preg_replace('/[0-9]+/', '', $str);
        };
    }

    public function onlyNumbers()
    {
        return function ($str) {
            return (int)preg_replace('/[^0-9]/', '', $str);
        };
    }
}
