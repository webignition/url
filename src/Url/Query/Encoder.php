<?php

namespace webignition\Url\Query;

use webignition\Url\Configuration;

class Encoder
{
    const PAIR_DELIMITER = '&';
    const KEY_VALUE_DELIMITER = '=';
    const FRAGMENT_IDENTIFIER = '#';

    const ENCODED_TILDE = '%7E';

    /**
     * Collection of characters that must be included if only minimally-encoding
     * query string keys
     *
     * @var string[]
     */
    private $verySpecialCharacters = [
        self::PAIR_DELIMITER => '%26',
        self::FRAGMENT_IDENTIFIER => '%23'
    ];

    private $pairs = [];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $nullValuePlaceholder = null;

    /**
     * @param array $pairs
     * @param Configuration|null $configuration
     */
    public function __construct(array $pairs, Configuration $configuration = null)
    {
        $this->pairs = $pairs;
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return str_replace(self::ENCODED_TILDE, '~', $this->buildQueryStringFromPairs());
    }

    /**
     *
     * @return string
     */
    private function buildQueryStringFromPairs()
    {
        foreach ($this->pairs as $key => $value) {
            if (is_null($value)) {
                $this->pairs[$key] = $this->getNullValuePlaceholder();
            }
        }

        $baseEncodedQuery = str_replace('=' . $this->getNullValuePlaceholder(), '', http_build_query($this->pairs));

        if ($this->hasConfiguration() && !$this->configuration->getFullyEncodeQueryStringKeys()) {
            $keyValuePairs = explode(self::PAIR_DELIMITER, $baseEncodedQuery);

            foreach ($keyValuePairs as $keyValuePairIndex => $keyValuePair) {
                $keyAndValue = explode(self::KEY_VALUE_DELIMITER, $keyValuePair);

                $keyAndValue[0] = str_replace(
                    array_keys($this->verySpecialCharacters),
                    array_values($this->verySpecialCharacters),
                    rawurldecode($keyAndValue[0])
                );

                $keyValuePairs[$keyValuePairIndex] = implode('=', $keyAndValue);
            }

            $baseEncodedQuery = implode(self::PAIR_DELIMITER, $keyValuePairs);
        }

        return $baseEncodedQuery;
    }

    /**
     * @return string
     */
    private function getNullValuePlaceholder()
    {
        if (is_null($this->nullValuePlaceholder)) {
            $placeholder = $this->generateNullValuePlaceholder();
            $values = array_values($this->pairs);

            while (in_array($placeholder, $values)) {
                $placeholder = $this->generateNullValuePlaceholder();
            }

            $this->nullValuePlaceholder = $placeholder;
        }

        return $this->nullValuePlaceholder;
    }

    /**
     * @return string
     */
    private function generateNullValuePlaceholder()
    {
        return md5(time());
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return bool
     */
    public function hasConfiguration()
    {
        return !is_null($this->configuration);
    }
}
