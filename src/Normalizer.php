<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Normalizer
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';
    const SCHEME_FILE = 'file';

    const PATH_SEPARATOR = '/';
    const QUERY_KEY_VALUE_DELIMITER = '&';

    const OPTION_DEFAULT_SCHEME = 'default-scheme';
    const OPTION_REMOVE_PATH_FILES_PATTERNS = 'remove-path-files-patterns';
    const OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS = 'remove-query-parameters-patterns';

    const PRESERVING_NORMALIZATIONS =
        self::CAPITALIZE_PERCENT_ENCODING |
        self::DECODE_UNRESERVED_CHARACTERS |
        self::CONVERT_EMPTY_HTTP_PATH |
        self::REMOVE_DEFAULT_FILE_HOST |
        self::REMOVE_DEFAULT_PORT |
        self::REMOVE_PATH_DOT_SEGMENTS |
        self::CONVERT_HOST_UNICODE_TO_PUNYCODE;

    // Semantically-lossless normalizations
    const CAPITALIZE_PERCENT_ENCODING = 1;
    const DECODE_UNRESERVED_CHARACTERS = 2;
    const CONVERT_EMPTY_HTTP_PATH = 4;
    const REMOVE_DEFAULT_FILE_HOST = 8;
    const REMOVE_DEFAULT_PORT = 16;
    const REMOVE_PATH_DOT_SEGMENTS = 32;
    const CONVERT_HOST_UNICODE_TO_PUNYCODE = 64;

    // Potentially-lossy normalizations
    const REDUCE_DUPLICATE_PATH_SLASHES = 128;
    const SORT_QUERY_PARAMETERS = 256;

    // Lossy normalizations
    const ADD_PATH_TRAILING_SLASH = 512;
    const REMOVE_USER_INFO = 1024;
    const REMOVE_FRAGMENT = 2048;
    const REMOVE_WWW = 4096;

    const NONE = 0;

    const HOST_STARTS_WITH_WWW_PATTERN = '/^www\./';
    const REMOVE_INDEX_FILE_PATTERN = '/^index\.[a-z]+$/i';

    public static function normalize(
        UriInterface $uri,
        int $flags = self::PRESERVING_NORMALIZATIONS,
        ?array $options = []
    ): UriInterface {
        if ($flags !== self::NONE) {
            if ($flags & self::REMOVE_USER_INFO && '' !== $uri->getUserInfo()) {
                $uri = $uri->withUserInfo('');
            }

            if ($flags & self::REMOVE_FRAGMENT && '' !== $uri->getFragment()) {
                $uri = $uri->withFragment('');
            }

            if ('' !== $uri->getHost()) {
                $host = $uri->getHost();

                if ($flags & self::CONVERT_HOST_UNICODE_TO_PUNYCODE) {
                    $host = PunycodeEncoder::encode($host);
                }

                if ($flags & self::REMOVE_WWW) {
                    if (preg_match(self::HOST_STARTS_WITH_WWW_PATTERN, $host) > 0) {
                        $host = preg_replace(self::HOST_STARTS_WITH_WWW_PATTERN, '', $host);
                    }
                }

                $uri = $uri->withHost($host);
            }

            if ($flags & self::REMOVE_PATH_DOT_SEGMENTS) {
                $uri = $uri->withPath(self::removePathDotSegments($uri->getPath()));
            }

            if ($flags & self::REDUCE_DUPLICATE_PATH_SLASHES) {
                $uri->withPath(preg_replace('#//++#', '/', $uri->getPath()));
            }

            if ($flags & self::ADD_PATH_TRAILING_SLASH) {
                $uri = $uri->withPath(self::addPathTrailingSlash($uri->getPath()));
            }

            if ($flags & self::SORT_QUERY_PARAMETERS && '' !== $uri->getQuery()) {
                $query = self::mutateQuery($uri->getQuery(), function (array &$queryKeyValues) {
                    sort($queryKeyValues);
                });

                $uri = $uri->withQuery($query);
            }

            if ($flags & self::DECODE_UNRESERVED_CHARACTERS) {
                $uri = self::applyPregReplaceCallbackToPathAndQuery(
                    $uri,
                    '/%(?:2D|2E|5F|7E|3[0-9]|[46][1-9A-F]|[57][0-9A])/i',
                    function (array $match) {
                        return rawurldecode($match[0]);
                    }
                );
            }

            if ($flags & self::REMOVE_DEFAULT_PORT) {
                if (DefaultPortIdentifier::isDefaultPort($uri->getScheme(), $uri->getPort())) {
                    $uri = $uri->withPort(null);
                }
            }

            if ($flags & self::CAPITALIZE_PERCENT_ENCODING) {
                $uri = self::applyPregReplaceCallbackToPathAndQuery(
                    $uri,
                    '/(?:%[A-Fa-f0-9]{2})++/',
                    function (array $match) {
                        return strtoupper($match[0]);
                    }
                );
            }

            if ($flags & self::CONVERT_EMPTY_HTTP_PATH && $uri->getPath() === '' &&
                (self::SCHEME_HTTP === $uri->getScheme() || self::SCHEME_HTTPS === $uri->getScheme())
            ) {
                $uri = $uri->withPath('/');
            }

            if ($flags & self::REMOVE_DEFAULT_FILE_HOST &&
                self::SCHEME_FILE === $uri->getScheme() && 'localhost' === $uri->getHost()) {
                $uri = $uri->withHost('');
            }
        }

        if (isset($options[self::OPTION_REMOVE_PATH_FILES_PATTERNS])) {
            $uri = $uri->withPath(self::removePathFiles(
                $uri->getPath(),
                $options[self::OPTION_REMOVE_PATH_FILES_PATTERNS]
            ));
        }

        if (isset($options[self::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS]) &&
            is_array($options[self::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS])) {
            $patterns = $options[self::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS];

            $query = self::mutateQuery($uri->getQuery(), function (array &$queryKeyValues) use ($patterns) {
                $queryKeyValues = call_user_func(
                    [Normalizer::class, 'removeQueryParameters'],
                    $queryKeyValues,
                    $patterns
                );
            });

            $uri = $uri->withQuery($query);
        }

        return $uri;
    }

    private static function removePathFiles(string $path, array $patterns): string
    {
        if ('' === $path) {
            return $path;
        }

        $pathObject = new Path($path);
        if (!$pathObject->hasFilename()) {
            return $path;
        }

        $filename = $pathObject->getFilename();

        $hasFilenameToRemove = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $filename) > 0) {
                $hasFilenameToRemove = true;
            }
        }

        if ($hasFilenameToRemove) {
            $path = (string) $pathObject;
            $pathParts = explode(self::PATH_SEPARATOR, $path);

            array_pop($pathParts);

            $path = implode(self::PATH_SEPARATOR, $pathParts);
        }

        return $path;
    }

    private static function removePathDotSegments(string $path): string
    {
        if ('' === $path || '/' === $path) {
            return $path;
        }

        if (in_array($path, ['/..', '/.'])) {
            return '/';
        }

        $lastCharacter = $path[-1];
        $pathParts = explode('/', $path);
        $normalisedPathParts = [];

        foreach ($pathParts as $pathPart) {
            if ('.' === $pathPart) {
                continue;
            }

            if ('..' === $pathPart) {
                array_pop($normalisedPathParts);
            } else {
                $normalisedPathParts[] = $pathPart;
            }
        }

        $path = implode('/', $normalisedPathParts);

        if (empty($path) && '/' === $lastCharacter) {
            $path = '/';
        }

        return $path;
    }

    private static function addPathTrailingSlash(string $path): string
    {
        if ('' === $path) {
            return '/';
        }

        $pathObject = new Path($path);

        if (!$pathObject->hasFilename() && !$pathObject->hasTrailingSlash()) {
            $path = $path . '/';
        }

        return $path;
    }

    private static function applyPregReplaceCallbackToPathAndQuery(
        UriInterface $uri,
        string $regex,
        callable $callback
    ): UriInterface {
        return
            $uri->withPath(
                preg_replace_callback($regex, $callback, $uri->getPath())
            )->withQuery(
                preg_replace_callback($regex, $callback, $uri->getQuery())
            );
    }

    private static function removeQueryParameters(array $queryKeyValues, array $patterns): array
    {
        foreach ($patterns as $pattern) {
            $queryKeyValues = array_filter(
                $queryKeyValues,
                function (string $keyValue) use ($pattern) {
                    return call_user_func([Normalizer::class, 'queryKeyValueKeyMatchesPattern'], $keyValue, $pattern);
                }
            );
        }

        return $queryKeyValues;
    }

    private static function queryKeyValueKeyMatchesPattern(string $keyValue, string $pattern): bool
    {
        $firstEqualsPosition = strpos($keyValue, '=');

        $key = $firstEqualsPosition
            ? substr($keyValue, 0, $firstEqualsPosition)
            : $keyValue;

        return preg_match($pattern, $key) === 0;
    }

    private static function mutateQuery(string $query, callable $mutator): string
    {
        $queryKeyValues = explode(self::QUERY_KEY_VALUE_DELIMITER, $query);
        $mutator($queryKeyValues);

        return implode(self::QUERY_KEY_VALUE_DELIMITER, $queryKeyValues);
    }
}
