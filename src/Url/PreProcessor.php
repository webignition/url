<?php

namespace webignition\Url;

/**
 * Operations performed on a URL before parsing
 */
class PreProcessor
{
    public static function preProcess(?string $url): string
    {
        // Unencoded leading or trailing whitespace is not allowed
        $url = trim($url);

        // Whitespace that is not a regular space character is not allowed
        // and should be removed.
        //
        // Not clearly spec'd anywhere but is the default behaviour of Chrome
        // and FireFox
        $url = str_replace(array("\t", "\r", "\n"), '', $url);

        return $url;
    }
}
