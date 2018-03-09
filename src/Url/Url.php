<?php

namespace webignition\Url;

use Etechnika\IdnaConvert\IdnaConvert;
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
     * Original unmodified source URL
     *
     * @var string
     */
    protected $originUrl = '';

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
     * @param string $originUrl
     */
    public function __construct($originUrl = null)
    {
        $this->init($originUrl);
        $this->configuration = new Configuration();
    }

    /**
     *
     * @param string $originUrl
     */
    public function init($originUrl)
    {
        $this->originUrl = PreProcessor::preProcess($originUrl);
        $this->parts = $this->createParser()->getParts();
    }

    /**
     * @return ParserInterface
     */
    protected function createParser()
    {
        return new Parser($this->originUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
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
                $host = IdnaConvert::decodeString($host);
            }

            $rawRootUrl .= $host;
        }

        if ($this->hasPort()) {
            $rawRootUrl .= ':' . $this->getPort();
        }

        return $rawRootUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function hasScheme()
    {
        return $this->hasPart(UrlInterface::PART_SCHEME);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->getPart(UrlInterface::PART_SCHEME);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme($scheme)
    {
        $scheme = trim($scheme);

        if (empty($scheme)) {
            $this->removePart(UrlInterface::PART_SCHEME);

            return true;
        }

        $this->updatePart(UrlInterface::PART_SCHEME, $scheme);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHost()
    {
        return $this->hasPart(UrlInterface::PART_HOST);
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->getPart(UrlInterface::PART_HOST);
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
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

        $this->updatePart(UrlInterface::PART_HOST, $host);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPort()
    {
        return $this->hasPart(UrlInterface::PART_PORT);
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->getPart(UrlInterface::PART_PORT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPort($port)
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

    /**
     * {@inheritdoc}
     */
    public function hasUser()
    {
        return $this->hasPart(UrlInterface::PART_USER);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->getPart(UrlInterface::PART_USER);
    }

    /**
     * {@inheritdoc}
     */
    public function setUser($user)
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

    /**
     * {@inheritdoc}
     */
    public function hasPass()
    {
        return $this->hasPart(UrlInterface::PART_PASS);
    }

    /**
     * {@inheritdoc}
     */
    public function getPass()
    {
        return $this->getPart(UrlInterface::PART_PASS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPass($pass)
    {
        // A pass cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }

        $this->updatePart(UrlInterface::PART_PASS, $pass);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath()
    {
        return $this->hasPart(UrlInterface::PART_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->getPart(UrlInterface::PART_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->updatePart(UrlInterface::PART_PATH, $path);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasQuery()
    {
        return $this->hasPart(UrlInterface::PART_QUERY);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        $query = $this->getPart(UrlInterface::PART_QUERY);
        if ($query instanceof Query && !$query->hasConfiguration()) {
            $query->setConfiguration($this->getConfiguration());
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery($query)
    {
        if (is_null($query)) {
            $this->removePart(UrlInterface::PART_QUERY);

            return true;
        }

        $query = trim($query);

        if ('?' === substr($query, 0, 1)) {
            $query = substr($query, 1);
        }

        $this->updatePart(UrlInterface::PART_QUERY, new Query($query));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFragment()
    {
        return $this->hasPart(UrlInterface::PART_FRAGMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->getPart(UrlInterface::PART_FRAGMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFragment($fragment)
    {
        $fragment = trim($fragment);

        if (empty($fragment)) {
            $this->removePart(UrlInterface::PART_FRAGMENT);

            return true;
        }

        $this->updatePart(UrlInterface::PART_FRAGMENT, ltrim($fragment, '#'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $url = $this->getRoot();

        $url .= $this->getPath();

        if ($this->hasQuery()) {
            $url .= '?' . $this->getQuery();
        }

        if ($this->hasFragment()) {
            $url .= '#' . $this->getFragment();
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function isRelative()
    {
        if ($this->hasScheme()) {
            return false;
        }

        if ($this->hasHost()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isProtocolRelative()
    {
        if ($this->hasScheme()) {
            return false;
        }

        return $this->hasHost();
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolute()
    {
        if ($this->isRelative()) {
            return false;
        }

        return !$this->isProtocolRelative();
    }

    /**
     * {@inheritdoc}
     */
    public function setPart($partName, $value)
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
                return $this->setFragment($value);
        }

        return false;
    }

    /**
     * Add a fragment to a URL that does not already have one
     *
     * @param string $fragment
     *
     * @return bool
     */
    public function addFragment($fragment)
    {
        if ($this->hasFragment()) {
            return false;
        }

        return $this->setFragment($fragment);
    }

    /**
     * @param string $partName
     * @param mixed $value
     */
    private function updatePart($partName, $value)
    {
        $this->parts[$partName] = $value;
        $this->init((string)$this);
    }

    /**
     * @param string $partName
     */
    private function removePart($partName)
    {
        if (array_key_exists($partName, $this->parts)) {
            unset($this->parts[$partName]);
            $this->init((string)$this);
        }
    }

    /**
     *  Add a path to a URL that does not already have one
     *
     * @param string $path
     *
     * @return bool
     */
    public function addPath($path)
    {
        if ($this->hasPath()) {
            return false;
        }

        return $this->setPath($path);
    }

    /**
     * @param int $port
     *
     * @return bool
     */
    public function addPort($port)
    {
        if ($this->hasPort()) {
            return false;
        }

        return $this->setPort($port);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCredentials()
    {
        return $this->hasUser() || $this->hasPass();
    }

    /**
     * @return string
     */
    private function getCredentials()
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
    protected function getPart($partName)
    {
        return isset($this->parts[$partName])
            ? $this->parts[$partName]
            : null;
    }

    /**
     * @param string $partName
     *
     * @return bool
     */
    protected function hasPart($partName)
    {
        return isset($this->parts[$partName]);
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
