<?php

namespace webignition\Url;

class NormalizerOptions
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const OPTION_DEFAULT_SCHEME = 'default-scheme';
    const OPTION_NORMALIZE_SCHEME = 'normalize-scheme';
    const OPTION_FORCE_HTTP = 'force-http';
    const OPTION_FORCE_HTTPS = 'force-https';

    /**
     * @var string
     */
    private $defaultScheme;

    /**
     * @var bool
     */
    private $normalizeScheme;

    /**
     * @var bool
     */
    private $forceHttp;

    /**
     * @var bool
     */
    private $forceHttps;

    public function __construct(array $options)
    {
        $this->defaultScheme = $options[self::OPTION_DEFAULT_SCHEME] ?? self::SCHEME_HTTP;
        $this->defaultScheme = trim($this->defaultScheme);

        $this->normalizeScheme = $options[self::OPTION_NORMALIZE_SCHEME] ?? true;
        $this->normalizeScheme = (bool) $this->normalizeScheme;

        $this->forceHttp = $options[self::OPTION_FORCE_HTTP] ?? null;
        if (null !== $this->forceHttp) {
            $this->forceHttp = (bool) $this->forceHttp;
        }

        $this->forceHttps = $options[self::OPTION_FORCE_HTTPS] ?? null;
        if (null !== $this->forceHttps) {
            $this->forceHttps = (bool) $this->forceHttps;
        }
    }

    public function getDefaultScheme(): string
    {
        return $this->defaultScheme;
    }

    public function getNormalizeScheme(): bool
    {
        return $this->normalizeScheme;
    }

    public function getForceHttp(): ?bool
    {
        return $this->forceHttp;
    }

    public function getForceHttps(): ?bool
    {
        return $this->forceHttps;
    }
}
