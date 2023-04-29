<?php

namespace App\Utils;

class Random
{
    public const NUMBERS = "0123456789";
    public const LOWERCASE_ALPHABET = "abcdefghijklmnopqrstuvwxyz";
    public const UPPERCASE_ALPHABET = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    public const HEXADECIMAL = "0123456789abcdef";

    public static function string($length, $chars): string
    {
        $retstr = '';
        $data = openssl_random_pseudo_bytes($length);
        $num_chars = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $retstr .= substr($chars, ord(substr($data, $i, 1)) % $num_chars, 1);
        }
        return $retstr;
    }
}
