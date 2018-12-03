<?php

namespace webignition\Url;

interface UrlInterface
{
    const PART_SCHEME = 'scheme';
    const PART_USER = 'user';
    const PART_PASS = 'pass';
    const PART_HOST = 'host';
    const PART_PORT = 'port';
    const PART_PATH = 'path';
    const PART_QUERY = 'query';
    const PART_FRAGMENT = 'fragment';

    public function init(string $originUrl);

    /**
     * @return string
     */
    public function getRoot();

    /**
     * @return bool
     */
    public function hasScheme();

    /**
     * @return string
     */
    public function getScheme();

    public function setScheme(?string $scheme);

    public function hasHost(): bool;

    public function getHost(): string;

    public function setHost(?string $host);

    public function hasPort(): bool;

    public function getPort(): ?int;

    /**
     * @param int|null $port
     *
     * @return bool
     */
    public function setPort($port): bool;

    public function hasUser(): bool;

    public function getUser(): string;

    public function setUser(?string $user): bool;

    public function hasPass(): bool;

    public function getPass(): string;

    public function setPass(?string $pass): bool;

    public function hasPath(): bool;

    public function getPath(): string;

    public function setPath(string $path);

    public function getQuery(): string;

    public function setQuery(?string $query);

    public function hasFragment(): bool;

    public function getFragment(): ?string;

    public function setFragment(?string $fragment);

    public function __toString(): string;

    public function isRelative(): bool ;

    public function isProtocolRelative(): bool ;

    public function isAbsolute(): bool ;

    /**
     * @param string $partName
     * @param mixed $value
     *
     * @return bool
     */
    public function setPart(string $partName, $value): bool;

    public function hasCredentials(): bool;
}
