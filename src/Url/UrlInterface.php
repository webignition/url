<?php

namespace webignition\Url;

use webignition\Url\Host\Host;
use webignition\Url\Path\Path;
use webignition\Url\Query\Query;

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

    /**
     *
     * @param string $originUrl
     */
    public function init($originUrl);

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

    /**
     * @param string $scheme
     *
     * @return bool
     */
    public function setScheme($scheme);

    /**
     * @return bool
     */
    public function hasHost();

    /**
     * @return Host
     */
    public function getHost();

    /**
     * @param string $host
     *
     * @return bool
     */
    public function setHost($host);

    /**
     * @return bool
     */
    public function hasPort();

    /**
     * @return int
     */
    public function getPort();

    /**
     * @param int $port
     *
     * @return bool
     */
    public function setPort($port);

    /**
     * @return bool
     */
    public function hasUser();

    /**
     * @return string
     */
    public function getUser();

    /**
     * @param string $user
     *
     * @return bool
     */
    public function setUser($user);

    /**
     * @return bool
     */
    public function hasPass();

    /**
     * @return string
     */
    public function getPass();

    /**
     * @param string $pass
     *
     * @return bool
     */
    public function setPass($pass);

    /**
     * @return bool
     */
    public function hasPath();

    /**
     * @return Path
     */
    public function getPath();

    /**
     * @param string $path
     *
     * @return bool
     */
    public function setPath($path);

    /**
     * @return Query
     */
    public function getQuery();

    /**
     * @param string $query
     *
     * @return bool
     */
    public function setQuery($query);

    /**
     * @return bool
     */
    public function hasFragment();

    /**
     * @return string
     */
    public function getFragment();

    /**
     * @param string $fragment
     *
     * @return bool
     */
    public function setFragment($fragment);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return bool
     */
    public function isRelative();

    /**
     * @return bool
     */
    public function isProtocolRelative();

    /**
     * @return bool
     */
    public function isAbsolute();

    /**
     * @param string $partName
     * @param string $value
     *
     * @return bool
     */
    public function setPart($partName, $value);

    /**
     * @return bool
     */
    public function hasCredentials();

    /**
     * @return bool
     */
    public function isPubliclyRoutable();
}
