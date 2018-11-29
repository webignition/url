<?php

namespace webignition\Url;

use webignition\Url\Query\Encoder;

class Normalizer
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const PORT_HTTP = 80;
    const PORT_HTTPS = 443;

    const PATH_SEPARATOR = '/';

    private $schemeToPortMap = [
        self::SCHEME_HTTP => self::PORT_HTTP,
        self::SCHEME_HTTPS => self::PORT_HTTPS,
    ];

    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    public function __construct()
    {
        $this->punycodeEncoder = new PunycodeEncoder();
    }

    public function normalize(UrlInterface $url, array $options): UrlInterface
    {
        $optionsObject = new NormalizerOptions($options);

        $normalizedUrl = clone $url;

        $this->normalizeScheme($normalizedUrl, $optionsObject);

        if ($optionsObject->getForceHttp()) {
            $normalizedUrl->setScheme(Normalizer::SCHEME_HTTP);
        }

        if ($optionsObject->getForceHttps()) {
            $normalizedUrl->setScheme(Normalizer::SCHEME_HTTPS);
        }

        if ($optionsObject->getRemoveUserInfo()) {
            $normalizedUrl->setUser(null);
            $normalizedUrl->setPass(null);
        }

        if ($optionsObject->getRemoveFragment()) {
            $normalizedUrl->setFragment(null);
        }

        if ($normalizedUrl->hasHost()) {
            $this->normalizeHost($normalizedUrl, $optionsObject);

            if ($optionsObject->getRemoveWww()) {
                $this->removeWww($normalizedUrl);
            }
        }

        if ($optionsObject->getRemoveKnownPorts()) {
            $this->removeKnownPorts($normalizedUrl);
        }

        if (!empty($optionsObject->getRemoveDefaultFilesPatterns())) {
            $this->removeDefaultFiles($normalizedUrl, $optionsObject);
        }

        $this->normalizePath($normalizedUrl, $optionsObject);

        if ($optionsObject->getSortQueryParameters()) {
            $this->sortQueryParameters($normalizedUrl);
        }

        return $normalizedUrl;
    }

    private function normalizeScheme(UrlInterface $url, NormalizerOptions $options)
    {
        if (!$url->hasScheme() && $options->getSetDefaultSchemeIfNoScheme()) {
            $url->setScheme($options->getDefaultScheme());
        }

        $url->setScheme(strtolower($url->getScheme()));
    }

    /**
     * Host normalization
     * - convert to lowercase
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
     * @param UrlInterface $url
     * @param NormalizerOptions $options
     */
    private function normalizeHost(UrlInterface $url, NormalizerOptions $options)
    {
        $hostObject = $url->getHost();

        $host = (string) $hostObject;

        if ($options->getConvertUnicodeToPunycode()) {
            $host = $this->punycodeEncoder->encode($host);
        }

        $host = strtolower($host);

        $hostHasTrailingDots = preg_match('/\.+$/', $host) > 0;
        if ($hostHasTrailingDots) {
            $host = rtrim($host, '.');
        }

        $url->setHost($host);
    }

    private function removeWww(UrlInterface $url)
    {
        $wwwPattern = '/^www\./';
        $hostObject = $url->getHost();

        $host = (string) $hostObject;

        if (preg_match($wwwPattern, $host) > 0) {
            $host = preg_replace($wwwPattern, '', $host);

            $url->setHost($host);
        }
    }

    private function removeKnownPorts(UrlInterface $url)
    {
        if ($url->hasPort() && $url->hasScheme()) {
            $port = $url->getPort();
            $scheme = $url->getScheme();

            $knownPort = $this->schemeToPortMap[$scheme] ?? null;

            if ($knownPort && $knownPort == $port) {
                $url->setPort(null);
            }
        }
    }

    private function removeDefaultFiles(UrlInterface $url, NormalizerOptions $options)
    {
        if (!$url->hasPath()) {
            return;
        }

        $pathObject = $url->getPath();
        if (!$pathObject->hasFilename()) {
            return;
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

            $url->setPath($updatedPath);
        }
    }

    private function normalizePath(UrlInterface $url, NormalizerOptions $options)
    {
        $this->reducePathTrailingSlashes($url);

        if ($options->getRemovePathDotSegments()) {
            $this->removePathDotSegments($url);
        }

        if ($options->getAddPathTrailingSlash()) {
            $this->addPathTrailingSlash($url);
        }
    }

    private function reducePathTrailingSlashes(UrlInterface $url)
    {
        if (!$url->hasPath()) {
            return;
        }

        $path = (string) $url->getPath();

        $lastCharacter = $path[-1];
        if ('/' !== $lastCharacter) {
            return;
        }

        $path = rtrim($path, '/') . '/';

        $url->setPath($path);
    }

    private function removePathDotSegments(UrlInterface $url)
    {
        $path = (string) $url->getPath();

        if ('/' === $path) {
            return;
        }

        $dotOnlyPaths = ['/..', '/.'];
        foreach ($dotOnlyPaths as $dotOnlyPath) {
            if ($dotOnlyPath === $path) {
                $url->setPath('/');

                return;
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

        $url->setPath($updatedPath);
    }

    private function addPathTrailingSlash(UrlInterface $url)
    {
        if ($url->hasPath()) {
            $pathObject = $url->getPath();

            if ($pathObject->hasFilename()) {
                return;
            }

            if (!$pathObject->hasTrailingSlash()) {
                $url->setPath((string) $pathObject . '/');
            }
        } else {
            $url->setPath('/');
        }
    }

    private function sortQueryParameters(UrlInterface $url)
    {
        $query = $url->getQuery();

        $parameters = $query->pairs();

        ksort($parameters);

        $queryEncoder = new Encoder($parameters);

        $url->setQuery((string) $queryEncoder);
    }
}
