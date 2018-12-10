<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class Url implements UriInterface
{
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

    public function __construct(string $url)
    {
        $components = Parser::parse($url);

        $scheme = $components[Parser::COMPONENT_SCHEME] ?? '';
        $host = $components[Parser::COMPONENT_HOST] ?? '';
        $port = $components[Parser::COMPONENT_PORT] ?? null;
        $path = $components[Parser::COMPONENT_PATH] ?? '';
        $query = $components[Parser::COMPONENT_QUERY] ?? '';
        $fragment = $components[Parser::COMPONENT_FRAGMENT] ?? '';
        $user = $components[Parser::COMPONENT_USER] ?? '';
        $pass = $components[Parser::COMPONENT_PASS] ?? '';

        $userInfo = new UserInfo($user, $pass);

        self::applyComponents($this, $scheme, (string) $userInfo, $host, $port, $path, $query, $fragment);
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

        return self::applyComponents(
            clone $this,
            $scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withUserInfo($user, $password = null)
    {
        $userInfo = (string) (new UserInfo($user, $password));

        if ($this->userInfo === $userInfo) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $userInfo,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withHost($host)
    {
        $host = trim(strtolower($host));

        if ($this->host === $host) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withPort($port)
    {
        if (null !== $port) {
            $port = (int) $port;
        }

        if ($this->port === $port) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withPath($path)
    {
        $path = Filter::filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $path,
            $this->query,
            $this->fragment
        );
    }

    public function withQuery($query)
    {
        $query = Filter::filterQueryOrFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $this->path,
            $query,
            $this->fragment
        );
    }

    public function withFragment($fragment)
    {
        $fragment = Filter::filterQueryOrFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $fragment
        );
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

    private static function applyComponents(
        Url $url,
        string $scheme,
        string $userInfo,
        string $host,
        ?int $port,
        string $path,
        string $query,
        string $fragment
    ): UriInterface {
        $url->scheme = strtolower($scheme);
        $url->userInfo = $userInfo;
        $url->host = strtolower($host);
        $url->path = Filter::filterPath($path);
        $url->query = Filter::filterQueryOrFragment($query);
        $url->fragment = Filter::filterQueryOrFragment($fragment);
        $url->port = Filter::filterPort($port, $url->getScheme());

        return $url;
    }
}
