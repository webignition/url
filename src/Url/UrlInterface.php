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
     */
    public function setPath($path);

    /**
     * @return bool
     */
    public function hasQuery();

    /**
     * @return Query
     */
    public function getQuery();

    /**
     * @param string $query
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
     */
    public function setPart($partName, $value);

    /**
     * Add a fragment to a URL that does not already have one
     *
     * @param string $fragment
     *
     * @return bool
     */
    public function addFragment($fragment);

    /**
     *  Add a path to a URL that does not already have one
     *
     * @param string $path
     * @return bool
     */
    public function addPath($path);

    /**
     * Add a port to a URL that does not already have one
     *
     * @param int $value
     *
     * @return bool
     */
    public function addPort($value);

    /**
     * @return bool
     */
    public function hasCredentials();
}
