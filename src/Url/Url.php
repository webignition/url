<?php

namespace webignition\Url;

use IpUtils\Exception\InvalidExpressionException;
use webignition\Url\Host\Host;
use webignition\Url\Path\Path;
use webignition\Url\Query\Query;

class Url implements UrlInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser = null;

    /**
     * @var Configuration
     */
    private $configuration = null;

    /**
     * Component parts of this URL, with keys:
     * -scheme
     * -host
     * -port
     * -user
     * -pass
     * -path
     * -query - after the question mark ?
     * -fragment - after the hashmark #
     *
     * @var array
     */
    private $parts = null;

    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    public function __construct(?string $originUrl = null)
    {
        $this->punycodeEncoder = new PunycodeEncoder();

        $this->configuration = new Configuration();
        $this->init($originUrl);
    }

    public function init(?string $originUrl)
    {
        $originUrl = PreProcessor::preProcess($originUrl);
        $this->parts = $this->createParser($originUrl)->getParts();

        $query = $this->parts[UrlInterface::PART_QUERY];
        $query->setConfiguration($this->configuration);
        $this->parts[UrlInterface::PART_QUERY] = $query;
    }

    protected function createParser(string $originUrl): ParserInterface
    {
        return new Parser($originUrl);
    }

    public function getRoot(): string
    {
        $rawRootUrl = '';

        if ($this->hasScheme()) {
            $rawRootUrl .= $this->getScheme() . ':';
        }

        if ($this->hasScheme() || $this->hasHost()) {
            $rawRootUrl .= '//';
        }

        if ($this->hasHost()) {
            if ($this->hasCredentials()) {
                $rawRootUrl .= $this->getCredentials() . '@';
            }

            $host = (string)$this->getHost();

            if ($this->getConfiguration()->getConvertIdnToUtf8()) {
                $host = $this->punycodeEncoder->decode($host);
            }

            $rawRootUrl .= $host;
        }

        if ($this->hasPort()) {
            $rawRootUrl .= ':' . $this->getPort();
        }

        return $rawRootUrl;
    }

    public function hasScheme(): bool
    {
        return $this->hasPart(UrlInterface::PART_SCHEME);
    }

    public function getScheme(): ?string
    {
        return $this->getPart(UrlInterface::PART_SCHEME);
    }

    public function setScheme(?string $scheme): bool
    {
        $scheme = trim($scheme);

        if (empty($scheme)) {
            $this->removePart(UrlInterface::PART_SCHEME);

            return true;
        }

        $this->updatePart(UrlInterface::PART_SCHEME, $scheme);

        return true;
    }

    public function hasHost(): bool
    {
        return $this->hasPart(UrlInterface::PART_HOST);
    }

    public function getHost(): ?Host
    {
        return $this->getPart(UrlInterface::PART_HOST);
    }

    public function setHost(?string $host): bool
    {
        if ($this->hasPath() && $this->getPath()->isRelative()) {
            $this->setPath('/' . $this->getPath());
        }

        if (empty($host)) {
            $this->removePart(UrlInterface::PART_SCHEME);
            $this->removePart(UrlInterface::PART_USER);
            $this->removePart(UrlInterface::PART_PASS);
            $this->removePart(UrlInterface::PART_PORT);
            $this->removePart(UrlInterface::PART_HOST);

            return true;
        }

        $this->updatePart(UrlInterface::PART_HOST, new Host($host));

        return true;
    }

    public function hasPort(): bool
    {
        return $this->hasPart(UrlInterface::PART_PORT);
    }

    public function getPort(): ?int
    {
        return $this->getPart(UrlInterface::PART_PORT);
    }

    public function setPort($port): bool
    {
        if (is_null($port)) {
            $this->removePart(UrlInterface::PART_PORT);

            return true;
        }

        $portTypeIsCorrect = ctype_digit($port) || is_int($port);

        if (!$portTypeIsCorrect) {
            return false;
        }

        $this->updatePart(UrlInterface::PART_PORT, $port);

        return true;
    }

    public function hasUser(): bool
    {
        return $this->hasPart(UrlInterface::PART_USER);
    }

    public function getUser(): ?string
    {
        return $this->getPart(UrlInterface::PART_USER);
    }

    public function setUser(?string $user): bool
    {
        if (is_null($user)) {
            $this->removePart(UrlInterface::PART_USER);

            return true;
        }

        $user = trim($user);

        // A user cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }

        $this->updatePart(UrlInterface::PART_USER, $user);

        return true;
    }

    public function hasPass(): bool
    {
        return $this->hasPart(UrlInterface::PART_PASS);
    }

    public function getPass(): ?string
    {
        return $this->getPart(UrlInterface::PART_PASS);
    }

    public function setPass(?string $pass): bool
    {
        // A pass cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }

        $this->updatePart(UrlInterface::PART_PASS, $pass);

        return true;
    }

    public function hasPath(): bool
    {
        return $this->hasPart(UrlInterface::PART_PATH);
    }

    public function getPath(): ?Path
    {
        return $this->getPart(UrlInterface::PART_PATH);
    }

    public function setPath(?string $path): bool
    {
        $this->updatePart(UrlInterface::PART_PATH, new Path($path));

        return true;
    }

    public function getQuery(): ?Query
    {
        return $this->getPart(UrlInterface::PART_QUERY);
    }

    public function setQuery(?string $query): bool
    {
        $query = trim($query);

        if ('?' === substr($query, 0, 1)) {
            $query = substr($query, 1);
        }

        $this->updatePart(UrlInterface::PART_QUERY, new Query($query));

        return true;
    }

    public function hasFragment(): bool
    {
        return $this->hasPart(UrlInterface::PART_FRAGMENT);
    }

    public function getFragment(): ?string
    {
        return $this->getPart(UrlInterface::PART_FRAGMENT);
    }

    public function setFragment(?string $fragment)
    {
        $fragment = trim($fragment);

        if (empty($fragment)) {
            $this->removePart(UrlInterface::PART_FRAGMENT);
        } else {
            $this->updatePart(UrlInterface::PART_FRAGMENT, ltrim($fragment, '#'));
        }
    }

    public function __toString(): string
    {
        $url = $this->getRoot();

        $url .= $this->getPath();

        $query = $this->getQuery();
        if (!$query->isEmpty()) {
            $url .= '?' . $this->getQuery();
        }

        if ($this->hasFragment()) {
            $url .= '#' . $this->getFragment();
        }

        return $url;
    }

    public function isRelative(): bool
    {
        if ($this->hasScheme()) {
            return false;
        }

        if ($this->hasHost()) {
            return false;
        }

        return true;
    }

    public function isProtocolRelative(): bool
    {
        if ($this->hasScheme()) {
            return false;
        }

        return $this->hasHost();
    }

    public function isAbsolute(): bool
    {
        if ($this->isRelative()) {
            return false;
        }

        return !$this->isProtocolRelative();
    }

    public function setPart(string $partName, $value): bool
    {
        switch ($partName) {
            case UrlInterface::PART_SCHEME:
                return $this->setScheme($value);

            case UrlInterface::PART_USER:
                return $this->setUser($value);

            case UrlInterface::PART_PASS:
                return $this->setPass($value);

            case UrlInterface::PART_HOST:
                return $this->setHost($value);

            case UrlInterface::PART_PORT:
                return $this->setPort($value);

            case UrlInterface::PART_PATH:
                return $this->setPath($value);

            case UrlInterface::PART_QUERY:
                return $this->setQuery($value);

            case UrlInterface::PART_FRAGMENT:
                $this->setFragment($value);

                return true;
        }

        return false;
    }

    /**
     * @param string $partName
     * @param mixed $value
     */
    private function updatePart(string $partName, $value)
    {
        $this->parts[$partName] = $value;
        $this->init((string)$this);
    }

    private function removePart(string $partName)
    {
        if (array_key_exists($partName, $this->parts)) {
            unset($this->parts[$partName]);
            $this->init((string)$this);
        }
    }

    public function hasCredentials(): bool
    {
        return $this->hasUser() || $this->hasPass();
    }

    private function getCredentials(): string
    {
        $credentials = '';

        if ($this->hasUser()) {
            $credentials .= $this->getUser();
        }

        if ($this->hasPass()) {
            $credentials .= ':';
            $credentials .= $this->getPass();
        }

        return $credentials;
    }

    /**
     * @param string $partName
     *
     * @return mixed
     */
    protected function getPart(string $partName)
    {
        return isset($this->parts[$partName])
            ? $this->parts[$partName]
            : null;
    }

    protected function hasPart(string $partName): bool
    {
        return isset($this->parts[$partName]);
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @return bool
     *
     * @throws InvalidExpressionException
     */
    public function isPubliclyRoutable(): bool
    {
        $host = $this->getHost();
        if (empty($host)) {
            return false;
        }

        if (!$host->isPubliclyRoutable()) {
            return false;
        }

        $hostContainsDots = substr_count($host, '.');
        if (!$hostContainsDots) {
            return false;
        }

        $hostStartsWithDot = strpos($host, '.') === 0;
        if ($hostStartsWithDot) {
            return false;
        }

        $hostEndsWithDot = strpos($host, '.') === strlen($host) - 1;
        if ($hostEndsWithDot) {
            return false;
        }

        return true;
    }
}
