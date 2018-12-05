<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Normalizer
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const PATH_SEPARATOR = '/';

    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    public function __construct()
    {
        $this->punycodeEncoder = new PunycodeEncoder();
    }

    public function normalize(UriInterface $uri, array $options): UriInterface
    {
        $optionsObject = new NormalizerOptions($options);

        if ('' === $uri->getScheme() && $optionsObject->getApplyDefaultSchemeIfNoScheme()) {
            $uri = $uri->withScheme($optionsObject->getDefaultScheme());
        }

        if ($optionsObject->getForceHttp()) {
            $uri = $uri->withScheme(self::SCHEME_HTTP);
        }

        if ($optionsObject->getForceHttps()) {
            $uri = $uri->withScheme(self::SCHEME_HTTPS);
        }

        if ($optionsObject->getRemoveUserInfo()) {
            $uri = $uri->withUserInfo('');
        }

        if ($optionsObject->getRemoveFragment()) {
            $uri = $uri->withFragment('');
        }

        if ('' !== $uri->getHost()) {
            $uri = $this->normalizeHost($uri, $optionsObject);

            if ($optionsObject->getRemoveWww()) {
                $uri = $this->removeWww($uri);
            }
        }

        if (!empty($optionsObject->getRemoveDefaultFilesPatterns())) {
            $uri = $this->removeDefaultFiles($uri, $optionsObject);
        }

        $uri = $this->normalizePath($uri, $optionsObject);

        if ($optionsObject->getSortQueryParameters()) {
            $uri = $this->sortQueryParameters($uri);
        }

        return $uri;
    }

    /**
     * Host normalization
     * - ascii version of IDN format
     * - trailing dot removal
     *
     * If host has trailing dots and there is no path, trim the trailing dots
     * e.g http://example.com. is interpreted as host=example.com. path=
     *     and needs to be understood as host=example.com and path=
     *
     *     http://example.com.. is interpreted as host=example.com.. path=
     *     and needs to be understood as host=example.com and path=
     *
     * @param UriInterface $uri
     * @param NormalizerOptions $options
     *
     * @return UriInterface
     */
    private function normalizeHost(UriInterface $uri, NormalizerOptions $options): UriInterface
    {
        $host = $uri->getHost();

        if ($options->getConvertUnicodeToPunycode()) {
            $host = $this->punycodeEncoder->encode($host);
        }

        $hostHasTrailingDots = preg_match('/\.+$/', $host) > 0;
        if ($hostHasTrailingDots) {
            $host = rtrim($host, '.');
        }

        $uri = $uri->withHost($host);

        return $uri;
    }

    private function removeWww(UriInterface $uri): UriInterface
    {
        $wwwPattern = '/^www\./';
        $host = $uri->getHost();

        if (preg_match($wwwPattern, $host) > 0) {
            $host = preg_replace($wwwPattern, '', $host);

            $uri = $uri->withHost($host);
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
        $uri = $this->reducePathTrailingSlashes($uri);

        if ($options->getRemovePathDotSegments()) {
            $uri = $this->removePathDotSegments($uri);
        }

        if ($options->getAddPathTrailingSlash()) {
            $uri = $this->addPathTrailingSlash($uri);
        }

        return $uri;
    }

    private function reducePathTrailingSlashes(UriInterface $uri): UriInterface
    {
        $path = $uri->getPath();
        if ('' === $path) {
            return $uri;
        }

        $lastCharacter = $path[-1];
        if ('/' !== $lastCharacter) {
            return $uri;
        }

        $path = rtrim($path, '/') . '/';

        return $uri->withPath($path);
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
