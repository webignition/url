<?php

namespace webignition\Url\Query;

use webignition\Url\Configuration;

class Encoder
{
    const PAIR_DELIMITER = '&';
    const KEY_VALUE_DELIMITER = '=';
    const FRAGMENT_IDENTIFIER = '#';
    const ENCODED_TILDE = '%7E';
    const DEFAULT_NULL_VALUE_PLACEHOLDER = 'NULL';
    const NULL_VALUE_PLACEHOLDER_MODIFIER = '-';

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
        $nullValuePlaceholder = $this->createNullValuePlaceholder();

        foreach ($this->pairs as $key => $value) {
            if (is_null($value)) {
                $this->pairs[$key] = $nullValuePlaceholder;
            }
        }

        $baseEncodedQuery = str_replace('=' . $nullValuePlaceholder, '', http_build_query($this->pairs));

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
    private function createNullValuePlaceholder()
    {
        $nullValuePlaceholder = self::DEFAULT_NULL_VALUE_PLACEHOLDER;
        $values = [];

        foreach ($this->pairs as $key => $value) {
            $values[] = $value;
        }

        while ($this->isNullValuePlaceholderPresentInQueryValues($nullValuePlaceholder, $values)) {
            $nullValuePlaceholder .= self::NULL_VALUE_PLACEHOLDER_MODIFIER;
        }

        return $nullValuePlaceholder;
    }

    /**
     * @param string $nullValuePlaceholder
     * @param array $values
     *
     * @return bool
     */
    private function isNullValuePlaceholderPresentInQueryValues($nullValuePlaceholder, array $values)
    {
        foreach ($values as $value) {
            if (substr_count($value, $nullValuePlaceholder)) {
                return true;
            }
        }

        return false;
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
