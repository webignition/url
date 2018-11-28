<?php

namespace webignition\Url;

class NormalizerOptions
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const OPTION_DEFAULT_SCHEME = 'default-scheme';
    const OPTION_NORMALIZE_SCHEME = 'normalize-scheme';

    /**
     * @var string
     */
    private $defaultScheme;

    /**
     * @var bool
     */
    private $normalizeScheme;

    public function __construct(array $options)
    {
        $this->defaultScheme = $options[self::OPTION_DEFAULT_SCHEME] ?? self::SCHEME_HTTP;
        $this->defaultScheme = trim($this->defaultScheme);

        $this->normalizeScheme = $options[self::OPTION_NORMALIZE_SCHEME] ?? true;
        $this->normalizeScheme = (bool) $this->normalizeScheme;

    }

    public function getDefaultScheme(): string
    {
        return $this->defaultScheme;
    }

    public function getNormalizeScheme(): bool
    {
        return $this->normalizeScheme;
    }
}
