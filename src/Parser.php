<?php

namespace webignition\Url;

class Parser
{
    const QUERY_DELIMITER = '?';
    const FRAGMENT_DELIMITER = '#';
    const PATH_DELIMITER = '/';

    const PROTOCOL_RELATIVE_START = '//';
    const PROTOCOL_RELATIVE_DUMMY_SCHEME = 'dummy';

    const PART_SCHEME = 'scheme';
    const PART_USER = 'user';
    const PART_PASS = 'pass';
    const PART_HOST = 'host';
    const PART_PORT = 'port';
    const PART_PATH = 'path';
    const PART_QUERY = 'query';
    const PART_FRAGMENT = 'fragment';

    /**
     * Scheme names consist of a sequence of characters beginning with a
     * letter and followed by any combination of letters, digits, plus
     * ("+"), period ("."), or hyphen ("-").
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    const SCHEME_ONLY_URL_PATTERN = '/^[a-z][a-z0-9+\.-]+:\/\/$/i';

    public function parse(string $url): array
    {
        $url = $this->normalizeWhitespace($url);

        if (self::PROTOCOL_RELATIVE_START === substr($url, 0, strlen(self::PROTOCOL_RELATIVE_START))) {
            $url = self::PROTOCOL_RELATIVE_DUMMY_SCHEME . ':' . $url;
        }

        $parts = $this->parseParts($url);

        if (strlen($url) && self::FRAGMENT_DELIMITER === $url[-1]) {
            $parts[self::PART_FRAGMENT] = '';
        }

        $scheme = isset($parts[self::PART_SCHEME])
            ? $parts[self::PART_SCHEME]
            : null;

        if (self::PROTOCOL_RELATIVE_DUMMY_SCHEME === $scheme) {
            unset($parts[self::PART_SCHEME]);
        }

        if (isset($parts[self::PART_PORT])) {
            $parts[self::PART_PORT] = (int)$parts[self::PART_PORT];
        }

        if (isset($parts[self::PART_PATH]) && empty($parts[self::PART_PATH])) {
            unset($parts[self::PART_PATH]);
        }

        return $parts;
    }

    private function normalizeWhitespace(string $url): string
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

    private function parseParts(string $url): array
    {
        $parts = parse_url($url);

        if (false !== $parts) {
            return $parts;
        }

        $parts = $this->parseUrlWithOnlyScheme($url);
        if (!empty($parts)) {
            return $parts;
        }

        $parts = $this->parseUrlWithInvalidPort($url);
        if (!empty($parts)) {
            return $parts;
        }

        return [];
    }

    private function parseUrlWithOnlyScheme(string $url): array
    {
        if (preg_match(self::SCHEME_ONLY_URL_PATTERN, $url)) {
            return [
                'scheme' => preg_replace('/:\/\/$/', '', $url),
            ];
        }

        return [];
    }

    private function parseUrlWithInvalidPort(string $url): array
    {
        $parts = $this->parseUrlWithInvalidPortWithPath($url);
        if (!empty($parts)) {
            return $parts;
        }

        $parts = $this->parseUrlWithInvalidPortWithoutPathWithQuery($url);
        if (!empty($parts)) {
            return $parts;
        }

        $parts = $this->parseUrlWithInvalidPOrtWithoutPathWithoutQueryWithFragment($url);
        if (!empty($parts)) {
            return $parts;
        }

        $parts = $this->parseUrlEndingWithPortPattern($url);
        if (!empty($parts)) {
            return $parts;
        }

        return [];
    }

    private function parseUrlWithInvalidPortWithPath(string $url): array
    {
        $doubleSlashPosition = strpos($url, '//');

        $firstSlashSearchOffset = false === $doubleSlashPosition
            ? 0
            : $doubleSlashPosition + 2;

        $firstSlashPosition = strpos($url, '/', $firstSlashSearchOffset);

        if (false === $firstSlashPosition) {
            return [];
        }

        return $this->parseUrlEndingWithPortPatternAndSuffix($url, $firstSlashPosition);
    }

    private function parseUrlWithInvalidPortWithoutPathWithQuery(string $url): array
    {
        $queryDelimiterPosition = strpos($url, self::QUERY_DELIMITER);

        if (false === $queryDelimiterPosition) {
            return [];
        }

        $fragmentDelimiterPosition = strpos($url, self::FRAGMENT_DELIMITER);

        if (false !== $fragmentDelimiterPosition && $fragmentDelimiterPosition < $queryDelimiterPosition) {
            return [];
        }

        return $this->parseUrlEndingWithPortPatternAndSuffix($url, $queryDelimiterPosition);
    }

    private function parseUrlWithInvalidPOrtWithoutPathWithoutQueryWithFragment(string $url): array
    {
        $fragmentDelimiterPosition = strpos($url, self::FRAGMENT_DELIMITER);

        if (false === $fragmentDelimiterPosition) {
            return [];
        }

        return $this->parseUrlEndingWithPortPatternAndSuffix($url, $fragmentDelimiterPosition);
    }

    private function parseUrlEndingWithPortPatternAndSuffix(string $url, int $suffixPosition)
    {
        $urlEndingWithPortPattern = substr($url, 0, $suffixPosition);
        $suffix = substr($url, $suffixPosition);

        return $this->parseUrlEndingWithPortPattern($urlEndingWithPortPattern, $suffix);
    }

    private function parseUrlEndingWithPortPattern(string $urlEndingWithPortPattern, string $postPortSuffix = '')
    {
        $endsWithPortPattern = '/\:[0-9]+$/';
        $endsWithPortMatches = [];

        if (preg_match($endsWithPortPattern, $urlEndingWithPortPattern, $endsWithPortMatches) > 0) {
            $modifiedUrl = preg_replace($endsWithPortPattern, '', $urlEndingWithPortPattern);
            $port = str_replace(':', '', $endsWithPortMatches[0]);

            $modifiedUrl .= $postPortSuffix;

            $parts = parse_url($modifiedUrl);
            $parts[self::PART_PORT] = $port;

            return $parts;
        }

        return [];
    }
}
