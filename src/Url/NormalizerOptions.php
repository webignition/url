<?php

namespace webignition\Url;

class NormalizerOptions
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const OPTION_DEFAULT_SCHEME = 'default-scheme';
    const OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME = 'set-default-scheme-if-no-scheme';
    const OPTION_FORCE_HTTP = 'force-http';
    const OPTION_FORCE_HTTPS = 'force-https';
    const OPTION_REMOVE_USER_INFO = 'remove-user-info';
    const OPTION_CONVERT_UNICODE_TO_PUNYCODE = 'convert-unicode-to-punycode';
    const OPTION_REMOVE_FRAGMENT = 'remove-fragment';
    const OPTION_REMOVE_WWW = 'remove-www';

    const DEFAULT_SCHEME = self::SCHEME_HTTP;
    const DEFAULT_SET_SCHEME_IF_NO_SCHEME = false;
    const DEFAULT_FORCE_HTTP = null;
    const DEFAULT_FORCE_HTTPS = null;
    const DEFAULT_REMOVE_USER_INFO = false;
    const DEFAULT_CONVERT_UNICODE_TO_PUNYCODE = true;
    const DEFAULT_REMOVE_FRAGMENT = false;
    const DEFAULT_REMOVE_WWW = false;

    /**
     * @var string
     */
    private $defaultScheme;

    /**
     * @var bool
     */
    private $setDefaultSchemeIfNoScheme;

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

    /**
     * @var bool
     */
    private $removeFragment;

    /**
     * @var bool
     */
    private $removeWww;

    public function __construct(array $options)
    {
        $this->defaultScheme = $options[self::OPTION_DEFAULT_SCHEME] ?? self::DEFAULT_SCHEME;
        $this->defaultScheme = trim($this->defaultScheme);

        $this->setDefaultSchemeIfNoScheme =
            $options[self::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME] ?? self::DEFAULT_SET_SCHEME_IF_NO_SCHEME;
        $this->setDefaultSchemeIfNoScheme = (bool) $this->setDefaultSchemeIfNoScheme;

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

        $this->convertUnicodeToPunycode =
            $options[self::OPTION_CONVERT_UNICODE_TO_PUNYCODE] ?? self::DEFAULT_CONVERT_UNICODE_TO_PUNYCODE;
        $this->convertUnicodeToPunycode = (bool) $this->convertUnicodeToPunycode;

        $this->removeFragment = $options[self::OPTION_REMOVE_FRAGMENT] ?? self::DEFAULT_REMOVE_FRAGMENT;
        $this->removeFragment = (bool) $this->removeFragment;

        $this->removeWww = $options[self::OPTION_REMOVE_WWW] ?? self::DEFAULT_REMOVE_WWW;
        $this->removeWww = (bool) $this->removeWww;
    }

    public function getDefaultScheme(): string
    {
        return $this->defaultScheme;
    }

    public function getSetDefaultSchemeIfNoScheme(): bool
    {
        return $this->setDefaultSchemeIfNoScheme;
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

    public function getRemoveFragment(): bool
    {
        return $this->removeFragment;
    }

    public function getRemoveWww(): bool
    {
        return $this->removeWww;
    }
}
