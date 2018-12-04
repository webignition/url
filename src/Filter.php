<?php

namespace webignition\Url;

class Filter
{
    public const MIN_PORT = 1;
    public const MAX_PORT = 65535;
    private const UNRESERVED_CHARACTERS = 'a-zA-Z0-9_\-\.~';
    private const CHARACTER_SUB_DELIMITERS = '!\$&\'\(\)\*\+,;=';

    public function filterPath(string $path): string
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        return preg_replace_callback(
            '/(?:[^' . self::UNRESERVED_CHARACTERS . self::CHARACTER_SUB_DELIMITERS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    public function filterQueryOrFragment(string $value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        return preg_replace_callback(
            '/(?:[^' . self::UNRESERVED_CHARACTERS . self::CHARACTER_SUB_DELIMITERS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $value
        );
    }

    private function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }

    /**
     * @param int|null $port
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException If the port is invalid.
     */
    public function filterPort(?int $port): ?int
    {
        if (null === $port) {
            return null;
        }

        $port = (int) $port;

        if (self::MIN_PORT > $port || self::MAX_PORT < $port) {
            throw new \InvalidArgumentException(
                sprintf('Invalid port: %d. Must be between %d and %d', $port, self::MIN_PORT, self::MAX_PORT)
            );
        }

        return $port;
    }
}
