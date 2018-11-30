<?php

namespace webignition\Url;

class Parser
{
    const FRAGMENT_SEPARATOR = '#';

    const PROTOCOL_RELATIVE_START = '//';
    const PROTOCOL_RELATIVE_DUMMY_SCHEME = 'dummy';

    /**
     * Scheme names consist of a sequence of characters beginning with a
     * letter and followed by any combination of letters, digits, plus
     * ("+"), period ("."), or hyphen ("-").
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    const PATTERN_SCHEME_ONLY_URL = '/^[a-z][a-z0-9+\.-]+:\/\/$/i';

    public function parse(string $url): array
    {
        $url = $this->normalizeWhitespace($url);

        if (self::PROTOCOL_RELATIVE_START === substr($url, 0, strlen(self::PROTOCOL_RELATIVE_START))) {
            $url = self::PROTOCOL_RELATIVE_DUMMY_SCHEME . ':' . $url;
        }

        $parts = parse_url($url);

        if (false === $parts) {
            $parts = $this->fixFailedParse($url);
        }

        if (self::FRAGMENT_SEPARATOR === substr($url, strlen($url) - 1)) {
            $parts[UrlInterface::PART_FRAGMENT] = '';
        }

        $scheme = isset($parts[UrlInterface::PART_SCHEME])
            ? $parts[UrlInterface::PART_SCHEME]
            : null;

        if (self::PROTOCOL_RELATIVE_DUMMY_SCHEME === $scheme) {
            unset($parts[UrlInterface::PART_SCHEME]);
        }

        if (isset($parts[UrlInterface::PART_PORT])) {
            $parts[UrlInterface::PART_PORT] = (int)$parts[UrlInterface::PART_PORT];
        }

        if (isset($parts[UrlInterface::PART_PATH]) && empty($parts[UrlInterface::PART_PATH])) {
            unset($parts[UrlInterface::PART_PATH]);
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

    private function fixFailedParse(?string $url): array
    {
        if (preg_match(self::PATTERN_SCHEME_ONLY_URL, $url)) {
            return [
                'scheme' => preg_replace('/:\/\/$/', '', $url),
            ];
        }

        return [];
    }
}
