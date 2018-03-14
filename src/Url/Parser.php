<?php

namespace webignition\Url;

use webignition\Url\Path\Path;
use webignition\Url\Query\Query;

class Parser implements ParserInterface
{
    const DEFAULT_PORT = 80;
    const MIN_PORT = 0;
    const MAX_PORT = 65535;

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

    /**
     * Collection of the different parts of the URL
     *
     * @var array
     */
    protected $parts = [];

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->parts = $this->parse($url);
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function parse($url)
    {
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

        if (empty($parts[UrlInterface::PART_QUERY])) {
            $parts[UrlInterface::PART_QUERY] = '';
        }

        $parts[UrlInterface::PART_QUERY] = new Query($parts[UrlInterface::PART_QUERY]);

        if (isset($parts[UrlInterface::PART_PATH])) {
            $parts[UrlInterface::PART_PATH] = new Path($parts[UrlInterface::PART_PATH]);
        }

        if (isset($parts[UrlInterface::PART_HOST])) {
            $parts[UrlInterface::PART_HOST] = new Host\Host($parts[UrlInterface::PART_HOST]);
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

        return $parts;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function fixFailedParse($url)
    {
        if (preg_match(self::PATTERN_SCHEME_ONLY_URL, $url)) {
            return [
                'scheme' => preg_replace('/:\/\/$/', '', $url),
            ];
        }

        return [];
    }
}
