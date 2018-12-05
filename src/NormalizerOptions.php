<?php

namespace webignition\Url;

class NormalizerOptions
{
    const OPTION_DEFAULT_SCHEME = 'default-scheme';
    const OPTION_APPLY_DEFAULT_SCHEME_IF_NO_SCHEME = 'apply-default-scheme-if-no-scheme';
    const OPTION_FORCE_HTTP = 'force-http';
    const OPTION_FORCE_HTTPS = 'force-https';
    const OPTION_REMOVE_USER_INFO = 'remove-user-info';
    const OPTION_CONVERT_UNICODE_TO_PUNYCODE = 'convert-unicode-to-punycode';
    const OPTION_REMOVE_FRAGMENT = 'remove-fragment';
    const OPTION_REMOVE_WWW = 'remove-www';
    const OPTION_REMOVE_DEFAULT_FILES_PATTERNS = 'remove-default-files-patterns';
    const OPTION_REMOVE_PATH_DOT_SEGMENTS = 'remove-path-dot-segments';
    const OPTION_ADD_PATH_TRAILING_SLASH = 'add-path-trailing-slash';
    const OPTION_SORT_QUERY_PARAMETERS = 'sort-query-parameters';

    const DEFAULT_SCHEME = Normalizer::SCHEME_HTTP;
    const DEFAULT_APPLY_SCHEME_IF_NO_SCHEME = false;
    const DEFAULT_FORCE_HTTP = false;
    const DEFAULT_FORCE_HTTPS = false;
    const DEFAULT_REMOVE_USER_INFO = false;
    const DEFAULT_CONVERT_UNICODE_TO_PUNYCODE = true;
    const DEFAULT_REMOVE_FRAGMENT = false;
    const DEFAULT_REMOVE_WWW = false;
    const DEFAULT_REMOVE_PATH_DOT_SEGMENTS = false;
    const DEFAULT_ADD_PATH_TRAILING_SLASH = false;
    const DEFAULT_SORT_QUERY_PARAMETERS = false;

    const REMOVE_INDEX_FILE_PATTERN = '/^index\.[a-z]+$/i';
    const REMOVE_DEFAULT_FILE_PATTERN = '/^default\.[a-z]+$/i';

    /**
     * @var string
     */
    private $defaultScheme;

    /**
     * @var bool
     */
    private $applyDefaultSchemeIfNoScheme;

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

    /**
     * @var string[]
     */
    private $removeDefaultFilesPatterns = [];

    /**
     * @var bool
     */
    private $removePathDotSegments;

    /**
     * @var bool
     */
    private $addPathTrailingSlash;

    /**
     * @var bool
     */
    private $sortQueryParameters;

    public function __construct(array $options = [])
    {
        $this->defaultScheme = $options[self::OPTION_DEFAULT_SCHEME] ?? self::DEFAULT_SCHEME;
        $this->defaultScheme = trim($this->defaultScheme);

        $this->applyDefaultSchemeIfNoScheme =
            $options[self::OPTION_APPLY_DEFAULT_SCHEME_IF_NO_SCHEME] ?? self::DEFAULT_APPLY_SCHEME_IF_NO_SCHEME;
        $this->applyDefaultSchemeIfNoScheme = (bool) $this->applyDefaultSchemeIfNoScheme;

        $this->forceHttp = $options[self::OPTION_FORCE_HTTP] ?? self::DEFAULT_FORCE_HTTP;
        if (null !== $this->forceHttp) {
            $this->forceHttp = (bool) $this->forceHttp;
        }

        $this->forceHttps = $options[self::OPTION_FORCE_HTTPS] ?? self::DEFAULT_FORCE_HTTPS;
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

        $removeDefaultFilesPatterns = $options[self::OPTION_REMOVE_DEFAULT_FILES_PATTERNS] ?? null;

        if (is_array($removeDefaultFilesPatterns)) {
            $this->removeDefaultFilesPatterns = $removeDefaultFilesPatterns;
        }

        $this->removePathDotSegments =
            $options[self::OPTION_REMOVE_PATH_DOT_SEGMENTS] ?? self::DEFAULT_REMOVE_PATH_DOT_SEGMENTS;
        $this->removePathDotSegments = (bool) $this->removePathDotSegments;

        $this->addPathTrailingSlash =
            $options[self::OPTION_ADD_PATH_TRAILING_SLASH] ?? self::DEFAULT_ADD_PATH_TRAILING_SLASH;
        $this->addPathTrailingSlash = (bool) $this->addPathTrailingSlash;

        $this->sortQueryParameters =
            $options[self::OPTION_SORT_QUERY_PARAMETERS] ?? self::DEFAULT_SORT_QUERY_PARAMETERS;
        $this->sortQueryParameters = (bool) $this->sortQueryParameters;
    }

    public function getDefaultScheme(): string
    {
        return $this->defaultScheme;
    }

    public function getApplyDefaultSchemeIfNoScheme(): bool
    {
        return $this->applyDefaultSchemeIfNoScheme;
    }

    public function getForceHttp(): bool
    {
        return $this->forceHttp;
    }

    public function getForceHttps(): bool
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

    /**
     * @return string[]
     */
    public function getRemoveDefaultFilesPatterns(): array
    {
        return $this->removeDefaultFilesPatterns;
    }

    public function getRemovePathDotSegments(): bool
    {
        return $this->removePathDotSegments;
    }

    public function getAddPathTrailingSlash(): bool
    {
        return $this->addPathTrailingSlash;
    }

    public function getSortQueryParameters(): bool
    {
        return $this->sortQueryParameters;
    }
}
