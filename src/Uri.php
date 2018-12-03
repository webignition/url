<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    const MIN_PORT = 1;
    const MAX_PORT = 65535;

    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';

    private $schemeToPortMap = [
        'http'  => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    /**
     * @var string
     */
    private $scheme = '';

    /**
     * @var string
     */
    private $userInfo = '';

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var int|null
     */
    private $port = null;

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var string
     */
    private $fragment = '';

    public function __construct(
        string $scheme,
        string $userInfo,
        string $host,
        ?int $port,
        string $path,
        string $query,
        string $fragment
    ) {
        $this->scheme = strtolower($scheme);
        $this->userInfo = $userInfo;
        $this->host = strtolower($host);
        $this->path = $this->filterPath($path);
        $this->query = $this->filterQueryAndFragment($query);
        $this->fragment = $this->filterQueryAndFragment($fragment);

        if (!empty($port)) {
            $knownPort = $this->schemeToPortMap[$scheme] ?? null;

            if ($knownPort && $port === $knownPort) {
                $port = null;
            }
        }

        $this->port = $this->filterPort($port);
    }

    public static function create(string $uri)
    {
        $parser = new Parser();

        $uriParts = $parser->parse($uri);

        $scheme = $uriParts[Parser::PART_SCHEME] ?? '';
        $host = $uriParts[Parser::PART_HOST] ?? '';
        $path = $uriParts[Parser::PART_PATH] ?? '';
        $query = $uriParts[Parser::PART_QUERY] ?? '';
        $fragment = $uriParts[Parser::PART_FRAGMENT] ?? '';
        $user = $uriParts[Parser::PART_USER] ?? '';
        $pass = $uriParts[Parser::PART_PASS] ?? '';

        $userInfo = UserInfoFactory::create($user, $pass);

        $port = null;
        if (isset($uriParts[Parser::PART_PORT])) {
            $port = $uriParts[Parser::PART_PORT];

            if (ctype_digit($port)) {
                $port = (int) $port;
            }

            if (!is_int($port)) {
                $port = null;
            }
        }

        return new static($scheme, $userInfo, $host, $port, $path, $query, $fragment);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (null !== $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        $scheme = trim(strtolower($scheme));

        if ($this->scheme === $scheme) {
            return $this;
        }

        return new Uri($scheme, $this->userInfo, $this->host, $this->port, $this->path, $this->query, $this->fragment);
    }

    public function withUserInfo($user, $password = null)
    {
        $userInfo = UserInfoFactory::create($user, $password);

        if ($this->userInfo === $userInfo) {
            return $this;
        }

        return new Uri($this->scheme, $userInfo, $this->host, $this->port, $this->path, $this->query, $this->fragment);
    }

    public function withHost($host)
    {
        $host = trim(strtolower($host));

        if ($this->host === $host) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $host, $this->port, $this->path, $this->query, $this->fragment);
    }

    public function withPort($port)
    {
        if (null !== $port) {
            $port = (int) $port;
        }

        if ($this->port === $port) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $port, $this->path, $this->query, $this->fragment);
    }

    public function withPath($path)
    {
        $path = $this->filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $this->port, $path, $this->query, $this->fragment);
    }

    public function withQuery($query)
    {
        $query = $this->filterQueryAndFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $this->port, $this->path, $query, $this->fragment);
    }

    public function withFragment($fragment)
    {
        $fragment = $this->filterQueryAndFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $this->port, $this->path, $this->query, $fragment);
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
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

    /**
     * @param int|null $port
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException If the port is invalid.
     */
    private function filterPort(?int $port): ?int
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
