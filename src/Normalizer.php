<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Normalizer
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';
    const SCHEME_FILE = 'file';

    const PATH_SEPARATOR = '/';

    const OPTION_DEFAULT_SCHEME = 'default-scheme';
    const OPTION_REMOVE_PATH_FILES_PATTERNS = 'remove-path-files-patterns';

    const PRESERVING_NORMALIZATIONS =
        self::CAPITALIZE_PERCENT_ENCODING |
        self::DECODE_UNRESERVED_CHARACTERS |
        self::CONVERT_EMPTY_HTTP_PATH |
        self::REMOVE_DEFAULT_FILE_HOST |
        self::REMOVE_DEFAULT_PORT |
        self::REMOVE_PATH_DOT_SEGMENTS;

    const CAPITALIZE_PERCENT_ENCODING = 1;
    const DECODE_UNRESERVED_CHARACTERS = 2;
    const CONVERT_EMPTY_HTTP_PATH = 4;
    const REMOVE_DEFAULT_FILE_HOST = 8;
    const REMOVE_DEFAULT_PORT = 16;
    const REMOVE_PATH_DOT_SEGMENTS = 32;

    const REMOVE_USER_INFO = 64;
    const CONVERT_HOST_UNICODE_TO_PUNYCODE = 128;
    const REMOVE_FRAGMENT = 256;
    const REMOVE_WWW = 512;
    const ADD_PATH_TRAILING_SLASH = 1024;
    const SORT_QUERY_PARAMETERS = 2048;
    const REDUCE_DUPLICATE_PATH_SLASHES = 4096;

    const HOST_STARTS_WITH_WWW_PATTERN = '/^www\./';
    const REMOVE_INDEX_FILE_PATTERN = '/^index\.[a-z]+$/i';

    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    public function __construct()
    {
        $this->punycodeEncoder = new PunycodeEncoder();
    }

    public function normalize(
        UriInterface $uri,
        int $flags = self::PRESERVING_NORMALIZATIONS,
        ?array $options = []
    ): UriInterface {
        if ($flags & self::REMOVE_USER_INFO && '' !== $uri->getUserInfo()) {
            $uri = $uri->withUserInfo('');
        }

        if ($flags & self::REMOVE_FRAGMENT && '' !== $uri->getFragment()) {
            $uri = $uri->withFragment('');
        }

        if ('' !== $uri->getHost()) {
            $host = $uri->getHost();

            if ($flags & self::CONVERT_HOST_UNICODE_TO_PUNYCODE) {
                $host = $this->punycodeEncoder->encode($host);
            }

            if ($flags & self::REMOVE_WWW) {
                if (preg_match(self::HOST_STARTS_WITH_WWW_PATTERN, $host) > 0) {
                    $host = preg_replace(self::HOST_STARTS_WITH_WWW_PATTERN, '', $host);
                }
            }

            $uri = $uri->withHost($host);
        }

        if (isset($options[self::OPTION_REMOVE_PATH_FILES_PATTERNS])) {
            $uri = $this->removePathFiles($uri, $options[self::OPTION_REMOVE_PATH_FILES_PATTERNS]);
        }

        if ($flags & self::REMOVE_PATH_DOT_SEGMENTS) {
            $uri = $this->removePathDotSegments($uri);
        }

        if ($flags & self::REDUCE_DUPLICATE_PATH_SLASHES) {
            $uri->withPath(preg_replace('#//++#', '/', $uri->getPath()));
        }

        if ($flags & self::ADD_PATH_TRAILING_SLASH) {
            $uri = $this->addPathTrailingSlash($uri);
        }

        if ($flags & self::SORT_QUERY_PARAMETERS && '' !== $uri->getQuery()) {
            $queryKeyValues = explode('&', $uri->getQuery());
            sort($queryKeyValues);
            $uri = $uri->withQuery(implode('&', $queryKeyValues));
        }

        if ($flags & self::DECODE_UNRESERVED_CHARACTERS) {
            $uri = $this->decodeUnreservedCharacters($uri);
        }

        if ($flags & self::REMOVE_DEFAULT_PORT) {
            if (DefaultPortIdentifier::isDefaultPort($uri->getScheme(), $uri->getPort())) {
                $uri = $uri->withPort(null);
            }
        }

        if ($flags & self::CAPITALIZE_PERCENT_ENCODING) {
            $uri = self::capitalizePercentEncoding($uri);
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

        return $uri;
    }

    private function removePathFiles(UriInterface $uri, array $patterns): UriInterface
    {
        $path = $uri->getPath();
        if ('' === $path) {
            return $uri;
        }

        $pathObject = new Path($path);
        if (!$pathObject->hasFilename()) {
            return $uri;
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

            $updatedPath = implode(self::PATH_SEPARATOR, $pathParts);

            $uri = $uri->withPath($updatedPath);
        }

        return $uri;
    }

    private function removePathDotSegments(UriInterface $uri): UriInterface
    {
        $path = $uri->getPath();
        if ('' === $path || '/' === $path) {
            return $uri;
        }

        $dotOnlyPaths = ['/..', '/.'];
        foreach ($dotOnlyPaths as $dotOnlyPath) {
            if ($dotOnlyPath === $path) {
                return $uri->withPath('/');
            }
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

        $updatedPath = implode('/', $normalisedPathParts);

        if (empty($updatedPath) && '/' === $lastCharacter) {
            $updatedPath = '/';
        }

        return $uri->withPath($updatedPath);
    }

    private function addPathTrailingSlash(UriInterface $uri): UriInterface
    {
        $path = $uri->getPath();

        if ('' === $path) {
            return $uri->withPath('/');
        }

        $pathObject = new Path($path);

        if ($pathObject->hasFilename()) {
            return $uri;
        }

        if (!$pathObject->hasTrailingSlash()) {
            $uri = $uri->withPath($path. '/');
        }

        return $uri;
    }

    private function decodeUnreservedCharacters(UriInterface $uri)
    {
        $regex = '/%(?:2D|2E|5F|7E|3[0-9]|[46][1-9A-F]|[57][0-9A])/i';

        $callback = function (array $match) {
            return rawurldecode($match[0]);
        };

        return
            $uri->withPath(
                preg_replace_callback($regex, $callback, $uri->getPath())
            )->withQuery(
                preg_replace_callback($regex, $callback, $uri->getQuery())
            );
    }

    private static function capitalizePercentEncoding(UriInterface $uri)
    {
        $regex = '/(?:%[A-Fa-f0-9]{2})++/';

        $callback = function (array $match) {
            return strtoupper($match[0]);
        };

        return
            $uri->withPath(
                preg_replace_callback($regex, $callback, $uri->getPath())
            )->withQuery(
                preg_replace_callback($regex, $callback, $uri->getQuery())
            );
    }
}
