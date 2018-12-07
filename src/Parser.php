<?php

namespace webignition\Url;

class Parser
{
    const QUERY_DELIMITER = '?';
    const FRAGMENT_DELIMITER = '#';
    const PATH_DELIMITER = '/';

    const PROTOCOL_RELATIVE_START = '//';
    const PROTOCOL_RELATIVE_DUMMY_SCHEME = 'dummy';

    const COMPONENT_SCHEME = 'scheme';
    const COMPONENT_USER = 'user';
    const COMPONENT_PASS = 'pass';
    const COMPONENT_HOST = 'host';
    const COMPONENT_PORT = 'port';
    const COMPONENT_PATH = 'path';
    const COMPONENT_QUERY = 'query';
    const COMPONENT_FRAGMENT = 'fragment';

    /**
     * Scheme names consist of a sequence of characters beginning with a
     * letter and followed by any combination of letters, digits, plus
     * ("+"), period ("."), or hyphen ("-").
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    const SCHEME_ONLY_URL_PATTERN = '/^[a-z][a-z0-9+\.-]+:\/\/$/i';

    public static function parse(string $url): array
    {
        $url = self::normalizeWhitespace($url);

        if (self::PROTOCOL_RELATIVE_START === substr($url, 0, strlen(self::PROTOCOL_RELATIVE_START))) {
            $url = self::PROTOCOL_RELATIVE_DUMMY_SCHEME . ':' . $url;
        }

        $components = self::parseComponents($url);

        if (strlen($url) && self::FRAGMENT_DELIMITER === $url[-1]) {
            $components[self::COMPONENT_FRAGMENT] = '';
        }

        $scheme = isset($components[self::COMPONENT_SCHEME])
            ? $components[self::COMPONENT_SCHEME]
            : null;

        if (self::PROTOCOL_RELATIVE_DUMMY_SCHEME === $scheme) {
            unset($components[self::COMPONENT_SCHEME]);
        }

        if (isset($components[self::COMPONENT_PORT])) {
            $components[self::COMPONENT_PORT] = (int)$components[self::COMPONENT_PORT];
        }

        if (isset($components[self::COMPONENT_PATH]) && empty($components[self::COMPONENT_PATH])) {
            unset($components[self::COMPONENT_PATH]);
        }

        return $components;
    }

    private static function normalizeWhitespace(string $url): string
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

    private static function parseComponents(string $url): array
    {
        $components = parse_url($url);

        if (false !== $components) {
            return $components;
        }

        $components = self::parseUrlWithOnlyScheme($url);
        if (!empty($components)) {
            return $components;
        }

        $components = self::parseUrlWithInvalidPort($url);
        if (!empty($components)) {
            return $components;
        }

        return [];
    }

    private static function parseUrlWithOnlyScheme(string $url): array
    {
        if (preg_match(self::SCHEME_ONLY_URL_PATTERN, $url)) {
            return [
                'scheme' => preg_replace('/:\/\/$/', '', $url),
            ];
        }

        return [];
    }

    private static function parseUrlWithInvalidPort(string $url): array
    {
        $components = self::parseUrlWithInvalidPortWithPath($url);
        if (!empty($components)) {
            return $components;
        }

        $components = self::parseUrlWithInvalidPortWithoutPathWithQuery($url);
        if (!empty($components)) {
            return $components;
        }

        $components = self::parseUrlWithInvalidPOrtWithoutPathWithoutQueryWithFragment($url);
        if (!empty($components)) {
            return $components;
        }

        $components = self::parseUrlEndingWithPortPattern($url);
        if (!empty($components)) {
            return $components;
        }

        return [];
    }

    private static function parseUrlWithInvalidPortWithPath(string $url): array
    {
        $doubleSlashPosition = strpos($url, '//');

        $firstSlashSearchOffset = false === $doubleSlashPosition
            ? 0
            : $doubleSlashPosition + 2;

        $firstSlashPosition = strpos($url, '/', $firstSlashSearchOffset);

        if (false === $firstSlashPosition) {
            return [];
        }

        return self::parseUrlEndingWithPortPatternAndSuffix($url, $firstSlashPosition);
    }

    private static function parseUrlWithInvalidPortWithoutPathWithQuery(string $url): array
    {
        $queryDelimiterPosition = strpos($url, self::QUERY_DELIMITER);

        if (false === $queryDelimiterPosition) {
            return [];
        }

        $fragmentDelimiterPosition = strpos($url, self::FRAGMENT_DELIMITER);

        if (false !== $fragmentDelimiterPosition && $fragmentDelimiterPosition < $queryDelimiterPosition) {
            return [];
        }

        return self::parseUrlEndingWithPortPatternAndSuffix($url, $queryDelimiterPosition);
    }

    private static function parseUrlWithInvalidPOrtWithoutPathWithoutQueryWithFragment(string $url): array
    {
        $fragmentDelimiterPosition = strpos($url, self::FRAGMENT_DELIMITER);

        if (false === $fragmentDelimiterPosition) {
            return [];
        }

        return self::parseUrlEndingWithPortPatternAndSuffix($url, $fragmentDelimiterPosition);
    }

    private static function parseUrlEndingWithPortPatternAndSuffix(string $url, int $suffixPosition)
    {
        $urlEndingWithPortPattern = substr($url, 0, $suffixPosition);
        $suffix = substr($url, $suffixPosition);

        return self::parseUrlEndingWithPortPattern($urlEndingWithPortPattern, $suffix);
    }

    private static function parseUrlEndingWithPortPattern(string $urlEndingWithPortPattern, string $postPortSuffix = '')
    {
        $endsWithPortPattern = '/\:[0-9]+$/';
        $endsWithPortMatches = [];

        if (preg_match($endsWithPortPattern, $urlEndingWithPortPattern, $endsWithPortMatches) > 0) {
            $modifiedUrl = preg_replace($endsWithPortPattern, '', $urlEndingWithPortPattern);
            $port = str_replace(':', '', $endsWithPortMatches[0]);

            $modifiedUrl .= $postPortSuffix;

            $components = parse_url($modifiedUrl);
            $components[self::COMPONENT_PORT] = $port;

            return $components;
        }

        return [];
    }
}
