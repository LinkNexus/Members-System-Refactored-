<?php

class Str
{
    public static function random($length): string
    {
        $alphabet = '0123456789qwertzuiopasdfghjklyxcvbnmQWERTZUIOPASDFGHJKLYXCVBNM';
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

}