<?php

namespace webignition\Url\Path;

use webignition\Url\PercentEncoder;

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

    public function __construct(?string $path)
    {
        $path = PercentEncoder::encodeUnreservedCharacters($path);

        $this->set($path);
    }

    public function isRelative(): bool
    {
        return !$this->isAbsolute();
    }

    public function isAbsolute(): bool
    {
        return substr($this->path, 0, 1) === self::PATH_PART_SEPARATOR;
    }

    public function get(): string
    {
        return $this->path;
    }

    public function set(?string $path)
    {
        $this->path = trim($path);
    }

    public function __toString(): string
    {
        return $this->get();
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
        return substr($this->get(), strlen($this->get()) - 1) == '/';
    }
}
