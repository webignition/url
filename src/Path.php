<?php

namespace webignition\Url;

class Path
{
    const PATH_PART_SEPARATOR = '/';

    /**
     * @var string
     */
    private $path = '';

    public function __construct(string $path)
    {
        $this->path = Filter::filterPath($path);
    }

    public function isRelative(): bool
    {
        return '' === $this->path
            ? true
            : self::PATH_PART_SEPARATOR !== $this->path[0];
    }

    public function isAbsolute(): bool
    {
        return '' === $this->path
            ? false
            : self::PATH_PART_SEPARATOR === $this->path[0];
    }

    public function __toString(): string
    {
        return $this->path;
    }

    public function hasFilename(): bool
    {
        if ('' === $this->path || self::PATH_PART_SEPARATOR === $this->path[-1]) {
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
        return '' === $this->path
            ? false
            : self::PATH_PART_SEPARATOR === $this->path[-1];
    }
}
