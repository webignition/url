<?php

namespace webignition\Url\Query;

use webignition\Url\Configuration;

class Query
{
    /**
     * @var ParserInterface
     */
    protected $parser = null;

    /**
     * Collection of key=value pairs
     *
     * @var array
     */
    protected $pairs = null;

    /**
     * @var Configuration
     */
    private $configuration = null;

    /**
     * @param string $encodedQueryString
     */
    public function __construct($encodedQueryString = '')
    {
        $this->init($encodedQueryString);
    }

    /**
     * @param $encodedQueryString
     */
    protected function init($encodedQueryString)
    {
        $this->parser = $this->createParser($encodedQueryString);
        $this->pairs = $this->parser->getKeyValuePairs();
    }

    /**
     * @param $encodedQueryString
     *
     * @return ParserInterface
     */
    protected function createParser($encodedQueryString)
    {
        return new Parser($encodedQueryString);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return str_replace(array('%7E'), array('~'), $this->buildQueryStringFromPairs());
    }

    /**
     *
     * @return string
     */
    private function buildQueryStringFromPairs()
    {
        $encoder = new Encoder($this->pairs(), $this->configuration);
        return (string)$encoder;
    }

    /**
     * @return array
     */
    public function pairs()
    {
        return $this->pairs;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function contains($key)
    {
        return array_key_exists($key, $this->pairs());
    }

    /**
     * @param string $encodedKey
     * @param string $encodedValue
     */
    public function set($encodedKey, $encodedValue)
    {
        $decodedKey = urldecode($encodedKey);

        if (is_null($encodedValue)) {
            unset($this->pairs[$decodedKey]);
        } else {
            $this->pairs[$decodedKey] = urldecode($encodedValue);
        }

        $this->init((string)$this);
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
    public function isEmpty()
    {
        return empty($this->pairs());
    }
}
