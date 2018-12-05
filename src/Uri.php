<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
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
        $this->path = Filter::filterPath($path);
        $this->query = Filter::filterQueryOrFragment($query);
        $this->fragment = Filter::filterQueryOrFragment($fragment);

        if (!empty($port)) {
            $knownPort = $this->schemeToPortMap[$scheme] ?? null;

            if ($knownPort && $port === $knownPort) {
                $port = null;
            }
        }

        $this->port = Filter::filterPort($port);
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

            if (ctype_digit($port) || is_int($port)) {
                $port = (int) $port;
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
        $path = Filter::filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $this->port, $path, $this->query, $this->fragment);
    }

    public function withQuery($query)
    {
        $query = Filter::filterQueryOrFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $this->port, $this->path, $query, $this->fragment);
    }

    public function withFragment($fragment)
    {
        $fragment = Filter::filterQueryOrFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        return new Uri($this->scheme, $this->userInfo, $this->host, $this->port, $this->path, $this->query, $fragment);
    }

    public function __toString()
    {
        $uri = '';

        if ('' !== $this->scheme) {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();

        if ('' !== $authority|| 'file' === $this->scheme) {
            $uri .= '//' . $authority;
        }

        $path = $this->path;

        if ($authority && $path && '/' !== $path[0]) {
            $path = '/' . $path;
        }

        if ('' === $authority && preg_match('/^\/\//', $path)) {
            $path = '/' . ltrim($path, '/');
        }

        $uri .= $path;

        if ('' !== $this->query) {
            $uri .= '?' . $this->query;
        }

        if ('' !== $this->fragment) {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }
}
