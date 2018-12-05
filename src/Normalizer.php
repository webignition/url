<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Normalizer
{
    const DEFAULT_SCHEME = 'http'; // needs to be configurable

    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const PATH_SEPARATOR = '/';

    const APPLY_DEFAULT_SCHEME_IF_NO_SCHEME = 1;
    const FORCE_HTTP = 2;
    const FORCE_HTTPS = 4;
    const REMOVE_USER_INFO = 8;
    const CONVERT_HOST_UNICODE_TO_PUNYCODE = 16;
    const REMOVE_FRAGMENT = 32;
    const REMOVE_WWW = 64;
    const REMOVE_DEFAULT_FILES_PATTERNS = 128;
    const REMOVE_PATH_DOT_SEGMENTS = 256;
    const ADD_PATH_TRAILING_SLASH = 512;
    const SORT_QUERY_PARAMETERS = 1024;
    const REDUCE_DUPLICATE_PATH_SLASHES = 2048;

    const PRESERVING_NORMALIZATIONS = 256;

    const HOST_STARTS_WITH_WWW_PATTERN = '/^www\./';

    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    public function __construct()
    {
        $this->punycodeEncoder = new PunycodeEncoder();
    }

    public function normalize(UriInterface $uri, int $flags = self::PRESERVING_NORMALIZATIONS): UriInterface
    {
        $optionsObject = new NormalizerOptions([]);

        if ($flags & self::APPLY_DEFAULT_SCHEME_IF_NO_SCHEME && '' === $uri->getScheme()) {
            $uri = $uri->withScheme($optionsObject->getDefaultScheme());
        }

        if ($flags & self::FORCE_HTTP && self::SCHEME_HTTP !== $uri->getScheme()) {
            $uri = $uri->withScheme(self::SCHEME_HTTP);
        }

        if ($flags & self::FORCE_HTTPS && self::SCHEME_HTTPS !== $uri->getScheme()) {
            $uri = $uri->withScheme(self::SCHEME_HTTPS);
        }

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

//        if (!empty($optionsObject->getRemoveDefaultFilesPatterns())) {
//            $uri = $this->removeDefaultFiles($uri, $optionsObject);
//        }

        if ($flags & self::REMOVE_PATH_DOT_SEGMENTS) {
            $uri = $this->removePathDotSegments($uri);
        }

        if ($flags & self::REDUCE_DUPLICATE_PATH_SLASHES) {
            $uri->withPath(preg_replace('#//++#', '/', $uri->getPath()));
        }

//        $uri = $this->normalizePath($uri, $optionsObject);

        if ($flags & self::SORT_QUERY_PARAMETERS) {
            $uri = $this->sortQueryParameters($uri);
        }

        return $uri;
    }

    private function removeDefaultFiles(UriInterface $uri, NormalizerOptions $options): UriInterface
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
        $filePatterns = $options->getRemoveDefaultFilesPatterns();

        $hasFilenameToRemove = false;
        foreach ($filePatterns as $filePattern) {
            if (preg_match($filePattern, $filename) > 0) {
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

    private function normalizePath(UriInterface $uri, NormalizerOptions $options): UriInterface
    {
        if ($options->getRemovePathDotSegments()) {
            $uri = $this->removePathDotSegments($uri);
        }

        if ($options->getAddPathTrailingSlash()) {
            $uri = $this->addPathTrailingSlash($uri);
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

    private function sortQueryParameters(UriInterface $uri): UriInterface
    {
        $query = $uri->getQuery();
        if ('' === $query) {
            return $uri;
        }

        $queryKeyValues = explode('&', $query);
        sort($queryKeyValues);

        return $uri->withQuery(implode('&', $queryKeyValues));
    }
}
