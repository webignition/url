<?php

namespace webignition\Url;

class PercentEncoder
{
    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';

    public static function encodeUnreservedCharacters(?string $path): string
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            function (array $match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }
}
