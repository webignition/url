<?php

namespace webignition\Url\Path;

class Path
{
    const PATH_PART_SEPARATOR = '/';

    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';

    /**
     * @var string
     */
    private $path = '';

    public function __construct(string $path)
    {
        $this->path = $this->filter($path);
    }

    public function isRelative(): bool
    {
        return !$this->isAbsolute();
    }

    public function isAbsolute(): bool
    {
        return substr($this->path, 0, 1) === self::PATH_PART_SEPARATOR;
    }

    public function __toString(): string
    {
        return $this->path;
    }

    public function hasFilename(): bool
    {
        if (substr($this->path, strlen($this->path) - 1) == '/') {
            return false;
        }

        return substr_count(basename($this->path), '.') > 0;
    }

    public function getFilename(): string
    {
        return $this->hasFilename() ? basename($this->path) : '';
    }

    public function getDirectory(): string
    {
        return $this->hasFilename() ? dirname($this->path) : $this->path;
    }

    public function hasTrailingSlash(): bool
    {
        return substr($this->path, strlen($this->path) - 1) == '/';
    }

    private function filter(string $path): string
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    private function rawurlencodeMatchZero(array $match)
    {
        return rawurlencode($match[0]);
    }
}
