<?php

namespace webignition\Url;

use Etechnika\IdnaConvert\IdnaConvert;

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
     * @var array
     */
    private $offsets = null;

    /**
     * @var string[]
     */
    private $availablePartNames = array(
        self::PART_SCHEME,
        self::PART_USER,
        self::PART_PASS,
        self::PART_HOST,
        self::PART_PORT,
        self::PART_PATH,
        self::PART_QUERY,
        self::PART_FRAGMENT,
    );

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
        $this->offsets = $this->createOffsets();
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

        if ($this->hasHost()) {
            $rawRootUrl .= '//';

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
        $this->setPart(UrlInterface::PART_SCHEME, $scheme);
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
        $this->setPart(UrlInterface::PART_HOST, $host);
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
        $this->setPart(UrlInterface::PART_PORT, $port);
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
        $this->setPart(UrlInterface::PART_USER, $user);
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
        $this->setPart(UrlInterface::PART_PASS, $pass);
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
        $this->setPart(UrlInterface::PART_PATH, $path);
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
        if ($query instanceof Query\Query && !$query->hasConfiguration()) {
            $query->setConfiguration($this->getConfiguration());
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery($query)
    {
        $this->setPart(UrlInterface::PART_QUERY, $query);
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
        $this->setPart(UrlInterface::PART_FRAGMENT, $fragment);
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
        if (!$this->hasPart($partName) && is_null($value)) {
            return;
        }

        if ($this->hasPart($partName)) {
            $this->replacePart($partName, $value);
        } else {
            $this->addPart($partName, $value);
        }

        $this->init($this->originUrl);
    }

    /**
     * @param string $partName
     * @param string $value
     */
    private function replacePart($partName, $value)
    {
        if ($partName == UrlInterface::PART_SCHEME && is_null($value)) {
            $this->originUrl = '//' . substr($this->originUrl, $this->offsets[UrlInterface::PART_HOST]);
            return;
        }

        if ($partName == UrlInterface::PART_QUERY && substr($value, 0, 1) == '?') {
            $value = substr($value, 1);
        }

        if ($partName == UrlInterface::PART_FRAGMENT && substr($value, 0, 1) == '#') {
            $value = substr($value, 1);
        }

        $isFragment = UrlInterface::PART_FRAGMENT === $partName;

        if ($isFragment && is_null($value) && isset($offsets[UrlInterface::PART_FRAGMENT])) {
            if ($this->getFragment() == '') {
                $this->originUrl = substr($this->originUrl, 0, $this->offsets[UrlInterface::PART_FRAGMENT]);
            } else {
                $this->originUrl = substr($this->originUrl, 0, $this->offsets[UrlInterface::PART_FRAGMENT] - 1);
            }

            return;
        }

        $this->originUrl = substr_replace(
            $this->originUrl,
            $value,
            $this->offsets[$partName],
            strlen($this->getPart($partName))
        );
    }

    /**
     * @param string $partName
     * @param string $value
     *
     * @return bool
     */
    private function addPart($partName, $value)
    {
        if ($partName == UrlInterface::PART_SCHEME) {
            return $this->addScheme($value);
        }

        if ($partName == UrlInterface::PART_USER) {
            return $this->addUser($value);
        }

        if ($partName == UrlInterface::PART_HOST) {
            return $this->addHost($value);
        }

        if ($partName == UrlInterface::PART_PASS) {
            return $this->addPass($value);
        }

        if ($partName == UrlInterface::PART_QUERY) {
            return $this->addQuery($value);
        }

        if ($partName == UrlInterface::PART_FRAGMENT) {
            return $this->addFragment($value);
        }

        if ($partName == UrlInterface::PART_PATH) {
            return $this->addPath($value);
        }

        if ($partName == UrlInterface::PART_PORT) {
            return $this->addPort($value);
        }

        return false;
    }


    /**
     * Add a scheme to a URL that does not already have one
     *
     * @param string $scheme
     *
     * @return bool
     */
    private function addScheme($scheme)
    {
        if ($this->hasScheme()) {
            return false;
        }

        if (!$this->isProtocolRelative()) {
            $this->originUrl = '//' . $this->originUrl;
        }

        if (substr($this->originUrl, 0, 1) != ':') {
            $this->originUrl = ':' . $this->originUrl;
        }

        $this->originUrl = $scheme . $this->originUrl;

        return true;
    }


    /**
     * Add a user to a URL that does not already have one
     *
     * @param string $user
     *
     * @return bool
     */
    private function addUser($user)
    {
        if ($this->hasUser()) {
            return false;
        }

        if (!is_string($user)) {
            $user = '';
        }

        $user = trim($user);

        // A user cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }

        $nextPartName = $this->getNextPartName(UrlInterface::PART_USER);

        if ($nextPartName == UrlInterface::PART_HOST) {
            $preNewPart = substr($this->originUrl, 0, $this->offsets[$nextPartName]);
            $postNewPart = substr($this->originUrl, $this->offsets[$nextPartName]);

            return $this->originUrl = $preNewPart . $user . '@' . $postNewPart;
        }

        $preNewPart = substr($this->originUrl, 0, $this->offsets[$nextPartName] - 1);
        $postNewPart = substr($this->originUrl, $this->offsets[$nextPartName] - 1);

        $this->originUrl = $preNewPart . $user . $postNewPart;

        return true;
    }

    /**
     * @param string $pass
     *
     * @return bool
     */
    private function addPass($pass)
    {
        if ($this->hasPass()) {
            return false;
        }

        // A pass cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }

        if ($this->hasUser()) {
            $preNewPart = substr($this->originUrl, 0, $this->offsets[UrlInterface::PART_HOST] - 1);
            $postNewPart = substr($this->originUrl, $this->offsets[UrlInterface::PART_HOST] - 1);

            $this->originUrl = $preNewPart . $pass . $postNewPart;

            return true;
        }

        $preNewPart = substr($this->originUrl, 0, $this->offsets[UrlInterface::PART_HOST]);
        $postNewPart = substr($this->originUrl, $this->offsets[UrlInterface::PART_HOST]);

        $this->originUrl = $preNewPart . ':' . $pass . '@' . $postNewPart;

        return true;
    }

    /**
     * Add a host to a URL that does not already have one
     *
     * @param string $host
     *
     * @return bool
     */
    private function addHost($host)
    {
        if ($this->hasHost()) {
            return false;
        }

        if ($this->hasPath() && $this->getPath()->isRelative()) {
            $this->setPath('/' . $this->getPath());
        }

        $this->originUrl = '//' . $host . $this->originUrl;

        return true;
    }

    /**
     * Add query to a URL that does not already have one
     *
     * @param string $query
     *
     * @return bool
     */
    private function addQuery($query)
    {
        if ($this->hasQuery()) {
            return false;
        }

        if (is_null($query)) {
            return true;
        }

        if (substr($query, 0, 1) != '?') {
            $query = '?' . $query;
        }

        if ($this->hasFragment()) {
            $preNewPart = substr($this->originUrl, 0, $this->offsets[UrlInterface::PART_FRAGMENT] - 1);
            $postNewPart = substr($this->originUrl, $this->offsets[UrlInterface::PART_FRAGMENT] - 1);

            $this->originUrl = $preNewPart . $query . $postNewPart;

            return true;
        }

        $this->originUrl .= $query;

        return true;
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

        if (!is_string($fragment)) {
            $fragment = '';
        }

        $fragment = trim($fragment);

        if ($fragment == '') {
            return true;
        }

        if (substr($fragment, 0, 1) != '#') {
            $fragment = '#' . $fragment;
        }

        $this->originUrl .= $fragment;

        return true;
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

        if (!$this->hasPart(UrlInterface::PART_QUERY) && !$this->hasPart(UrlInterface::PART_FRAGMENT)) {
            $this->originUrl = $this->originUrl . $path;

            return true;
        }

        $nextPartName = $this->getNextPartName(UrlInterface::PART_PATH);

        $offset = $this->offsets[$nextPartName];

        if ($nextPartName == UrlInterface::PART_FRAGMENT && $offset > 0) {
            $offset -= 1;
        }

        $this->originUrl = substr($this->originUrl, 0, $offset) . $path . substr($this->originUrl, $offset);

        return true;
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function addPort($value)
    {
        if (!$value or $this->hasPort()) {
            return false;
        }

        $this->parts[UrlInterface::PART_PORT] = $value;
        $host = $this->getHost();

        $this->originUrl = str_replace($host, $host.':'.$value, $this->originUrl);

        return true;
    }

    /**
     * Get the next url part after $partName that is present in this
     * url
     *
     * @param string $partName
     *
     * @return string|null
     */
    private function getNextPartName($partName)
    {
        $hasFoundPart = false;
        foreach ($this->availablePartNames as $availablePartName) {
            if ($partName == $availablePartName) {
                $hasFoundPart = true;
            }

            if ($hasFoundPart === false) {
                continue;
            }

            if ($availablePartName != $partName && $this->hasPart($availablePartName)) {
                return $availablePartName;
            }
        }

        return null;
    }

    private function createOffsets()
    {
        $offsets = [];

        $partNames = [];
        foreach ($this->availablePartNames as $availablePartName) {
            if ($this->hasPart($availablePartName)) {
                $partNames[] = $availablePartName;
            }
        }

        if (count($partNames) == 1) {
            $offsets = [
                $partNames[0] => 0
            ];

            return $offsets;
        }

        $originUrlComparison = str_split(rawurldecode($this->originUrl));
        $index = 0;

        foreach ($partNames as $partName) {
            $currentPart = urldecode((string)$this->parts[$partName]);

            // Special case: empty user (i.e. user = '', not null or missing user)
            if ($partName == UrlInterface::PART_USER && $currentPart == '') {
                if (array_slice($originUrlComparison, 0, 3) ==  [':', '/', '/']) {
                    $schemeLength = strlen(urldecode((string)$this->parts[UrlInterface::PART_SCHEME]));

                    $offsets[UrlInterface::PART_USER] = $offsets[UrlInterface::PART_SCHEME] + $schemeLength + 3;
                    continue;
                } elseif (array_slice($originUrlComparison, 0, 2) ==  ['/', '/']) {
                    $offsets[UrlInterface::PART_USER] = 2;
                    continue;
                }
            }

            // Special case: empty password
            if ($partName == UrlInterface::PART_PASS && $currentPart == '') {
                $userLength = strlen($this->parts[UrlInterface::PART_USER]);

                $offsets[UrlInterface::PART_PASS] = $offsets[UrlInterface::PART_USER] + $userLength + 1;
                continue;
            }

            $currentPartMatch = '';
            $currentPartFirstCharacter = substr($currentPart, 0, 1);

            while ($currentPartMatch != $currentPart) {
                if (!isset($originUrlComparison[0])) {
                    break;
                }

                $currentCharacter = $originUrlComparison[0];

                $nextCharacter = array_shift($originUrlComparison);
                $index++;

                if ($currentPartMatch == '' && $nextCharacter != $currentPartFirstCharacter) {
                    continue;
                }

                $currentPartMatch .= $currentCharacter;
            }

            $offset = $index - strlen($currentPartMatch);
            $offsets[$partName] = $offset;
        }

        return $offsets;
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
