<?php

namespace webignition\Url\Path;

/**
 * Represents the path part of a URL
 */
class Path
{
    const PATH_PART_SEPARATOR = '/';

    /**
     * @var string
     */
    private $path = '';

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->set($path);
    }

    /**
     * @return bool
     */
    public function isRelative()
    {
        return !$this->isAbsolute();
    }

    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return substr($this->path, 0, 1) === self::PATH_PART_SEPARATOR;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function set($path)
    {
        $this->path = trim($path);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return bool
     */
    public function hasFilename()
    {
        if (substr($this->path, strlen($this->path) - 1) == '/') {
            return false;
        }

        return substr_count(basename($this->path), '.') > 0;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->hasFilename() ? basename($this->path) : '';
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->hasFilename() ? dirname($this->path) : $this->path;
    }

    /**
     * @return bool
     */
    public function hasTrailingSlash()
    {
        return substr($this->get(), strlen($this->get()) - 1) == '/';
    }
}
