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
    const OPTION_REMOVE_USER_INFO = 'remove-user-info';
    const OPTION_CONVERT_UNICODE_TO_PUNYCODE = 'convert-unicode-to-punycode';

    const DEFAULT_SCHEME = self::SCHEME_HTTP;
    const DEFAULT_NORMALIZE_SCHEME = true;
    const DEFAULT_FORCE_HTTP = null;
    const DEFAULT_FORCE_HTTPS = null;
    const DEFAULT_REMOVE_USER_INFO = false;
    const DEFAULT_CONVERT_UNICODE_TO_PUNYCODE = true;

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

    /**
     * @var bool
     */
    private $removeUserInfo;

    /**
     * @var bool
     */
    private $convertUnicodeToPunycode;

    public function __construct(array $options)
    {
        $this->defaultScheme = $options[self::OPTION_DEFAULT_SCHEME] ?? self::DEFAULT_SCHEME;
        $this->defaultScheme = trim($this->defaultScheme);

        $this->normalizeScheme = $options[self::OPTION_NORMALIZE_SCHEME] ?? self::DEFAULT_NORMALIZE_SCHEME;
        $this->normalizeScheme = (bool) $this->normalizeScheme;

        $this->forceHttp = $options[self::OPTION_FORCE_HTTP] ?? null;
        if (null !== $this->forceHttp) {
            $this->forceHttp = (bool) $this->forceHttp;
        }

        $this->forceHttps = $options[self::OPTION_FORCE_HTTPS] ?? null;
        if (null !== $this->forceHttps) {
            $this->forceHttps = (bool) $this->forceHttps;
        }

        $this->removeUserInfo = $options[self::OPTION_REMOVE_USER_INFO] ?? self::DEFAULT_REMOVE_USER_INFO;
        $this->removeUserInfo = (bool) $this->removeUserInfo;

        $this->convertUnicodeToPunycode = $options[self::OPTION_CONVERT_UNICODE_TO_PUNYCODE]
            ?? self::DEFAULT_CONVERT_UNICODE_TO_PUNYCODE;
        $this->convertUnicodeToPunycode = (bool) $this->convertUnicodeToPunycode;
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

    public function getRemoveUserInfo(): bool
    {
        return $this->removeUserInfo;
    }

    public function getConvertUnicodeToPunycode(): bool
    {
        return $this->convertUnicodeToPunycode;
    }
}
