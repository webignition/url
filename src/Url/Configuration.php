<?php

namespace webignition\Url;

class Configuration
{
    /**
     * Whether to fully url-encode query string keys
     * Default is true
     *
     * When false, query string keys will be minimally encoded
     * At the very least, must encode: # &
     *
     * @var bool
     */
    private $fullyEncodeQueryStringKeys = true;

    /**
     * @var bool
     */
    private $convertIdnToUtf8 = false;

    public function enableFullyEncodeQueryStringKeys()
    {
        $this->fullyEncodeQueryStringKeys = true;
    }

    public function disableFullyEncodeQueryStringKeys()
    {
        $this->fullyEncodeQueryStringKeys = false;
    }

    public function enableConvertIdnToUtf8()
    {
        $this->convertIdnToUtf8 = true;
    }

    public function disableConvertIdnToUtf8()
    {
        $this->convertIdnToUtf8 = false;
    }

    /**
     * @return bool
     */
    public function getConvertIdnToUtf8()
    {
        return $this->convertIdnToUtf8;
    }

    /**
     * @return bool
     */
    public function getFullyEncodeQueryStringKeys()
    {
        return $this->fullyEncodeQueryStringKeys;
    }
}
