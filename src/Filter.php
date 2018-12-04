<?php

namespace webignition\Url;

class Filter
{
    public const MIN_PORT = 1;
    public const MAX_PORT = 65535;
    private const UNRESERVED_CHARACTERS = 'a-zA-Z0-9_\-\.~';
    private const CHARACTER_SUB_DELIMITERS = '!\$&\'\(\)\*\+,;=';
    private const TWO_CHARACTER_HEX_STRING = '[A-Fa-f0-9]{2}';

    public static function filterPath(string $path): string
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        $pattern = '/(?:[^'
            . self::UNRESERVED_CHARACTERS
            . self::CHARACTER_SUB_DELIMITERS
            . '%:@\/]++|%(?!'
            . self::TWO_CHARACTER_HEX_STRING
            . '))/';

        return self::pregReplaceCallbackRawUrlEncodeMatchZero($pattern, $path);
    }

    public static function filterQueryOrFragment(string $value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        $pattern = '/(?:[^'
            . self::UNRESERVED_CHARACTERS
            . self::CHARACTER_SUB_DELIMITERS
            . '%:@\/\?]++|%(?!'
            . self::TWO_CHARACTER_HEX_STRING
            . '))/';

        return self::pregReplaceCallbackRawUrlEncodeMatchZero($pattern, $value);
    }

    private static function pregReplaceCallbackRawUrlEncodeMatchZero(string $pattern, string $value): string
    {
        return preg_replace_callback(
            $pattern,
            function (array $match) {
                return rawurlencode($match[0]);
            },
            $value
        );
    }

    /**
     * @param int|null $port
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException If the port is invalid.
     */
    public static function filterPort(?int $port): ?int
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
