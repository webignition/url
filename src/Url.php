<?php

namespace webignition\Url;

class Url implements UrlInterface
{
    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';

    /**
     * @var Parser
     */
    private $parser;

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

    public function __construct(string $originUrl)
    {
        $this->parser = new Parser();
        $this->punycodeEncoder = new PunycodeEncoder();

        $this->configuration = new Configuration();
        $this->init($originUrl);
    }

    public function init(string $originUrl)
    {
        $this->parts = $this->parser->parse($originUrl);

//        $query = $this->parts[UrlInterface::PART_QUERY];
//        $query->setConfiguration($this->configuration);
//        $this->parts[UrlInterface::PART_QUERY] = $query;
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

    public function setScheme(?string $scheme)
    {
        $scheme = trim($scheme);

        if (empty($scheme)) {
            $this->removePart(UrlInterface::PART_SCHEME);
        } else {
            $this->updatePart(UrlInterface::PART_SCHEME, $scheme);
        }
    }

    public function hasHost(): bool
    {
        return $this->hasPart(UrlInterface::PART_HOST);
    }

    public function getHost(): string
    {
        $host = $this->getPart(UrlInterface::PART_HOST);

        if (null === $host) {
            $host = '';
        }

        return $host;
    }

    public function setHost(?string $host)
    {
        if ($this->hasPath()) {
            $path = $this->getPath();

            $isRelative = '/' !== $path[0];

            if ($isRelative) {
                $this->setPath('/' . $this->getPath());
            }
        }

        if (empty($host)) {
            $this->removePart(UrlInterface::PART_SCHEME);
            $this->removePart(UrlInterface::PART_USER);
            $this->removePart(UrlInterface::PART_PASS);
            $this->removePart(UrlInterface::PART_PORT);
            $this->removePart(UrlInterface::PART_HOST);
        } else {
            $this->updatePart(UrlInterface::PART_HOST, $host);
        }
    }

    public function hasPort(): bool
    {
        return $this->hasPart(UrlInterface::PART_PORT);
    }

    public function getPort(): ?int
    {
        $port = $this->getPart(UrlInterface::PART_PORT);

        if ('' === $port) {
            $port = null;
        }

        return $port;
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

        if (null === $pass && empty($this->getUser())) {
            $this->parts[self::PART_USER] = null;
        }

        $this->updatePart(UrlInterface::PART_PASS, $pass);

        return true;
    }

    public function hasPath(): bool
    {
        return $this->hasPart(UrlInterface::PART_PATH);
    }

    public function getPath(): string
    {
        $path = $this->getPart(UrlInterface::PART_PATH);

        if (null === $path) {
            $path = '';
        }

        return $path;
    }

    public function setPath(string $path)
    {
        $path = $this->filterPath($path);

        $this->updatePart(UrlInterface::PART_PATH, $path);
    }

    public function getQuery(): string
    {
        $query = $this->getPart(UrlInterface::PART_QUERY);

        if (null === $query) {
            $query = '';
        }

        return $query;
    }

    public function setQuery(?string $query)
    {
        $query = trim($query);

        if ('?' === substr($query, 0, 1)) {
            $query = substr($query, 1);
        }

        $query = $this->filterQueryAndFragment($query);

        $this->updatePart(UrlInterface::PART_QUERY, $query);
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
        if (!empty($query)) {
            $url .= '?' . $query;
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
                $this->setScheme($value);

                return true;

            case UrlInterface::PART_USER:
                return $this->setUser($value);

            case UrlInterface::PART_PASS:
                return $this->setPass($value);

            case UrlInterface::PART_HOST:
                $this->setHost($value);

                return true;

            case UrlInterface::PART_PORT:
                return $this->setPort($value);

            case UrlInterface::PART_PATH:
                $this->setPath($value);

                return true;

            case UrlInterface::PART_QUERY:
                $this->setQuery($value);

                return true;

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
        return isset($this->parts[$partName]) && null !== $this->parts[$partName];
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * Filters the path of a URI
     *
     * @param string $path
     *
     * @return string
     *
     * @throws \InvalidArgumentException If the path is invalid.
     */
    private function filterPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the query string or fragment of a URI.
     *
     * @param string $str
     *
     * @return string
     *
     * @throws \InvalidArgumentException If the query or fragment is invalid.
     */
    private function filterQueryAndFragment($str)
    {
        if (!is_string($str)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $str
        );
    }

    private function rawurlencodeMatchZero(array $match)
    {
        return rawurlencode($match[0]);
    }
}
