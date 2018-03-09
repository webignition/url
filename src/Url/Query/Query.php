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
     * @deprecated Deprecated since 1.9.17, to be removed in 2.0. No alternative, no need to get the query parser.
     *
     * @return ParserInterface
     */
    public function getParser()
    {
        @trigger_error(
            'getParser() is deprecated since 1.9.17, to be removed in 2.0. ' .
            'No alternative, no need to get the query parser'
        );

        return $this->parser;
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
     * @deprecated Deprecated since 1.9.18, to be removed in 2.0. Use set() instead.
     *
     * @param string $encodedKey
     * @param string $encodedValue
     */
    public function add($encodedKey, $encodedValue)
    {
        @trigger_error(
            'add() is deprecated since 1.9.18, to be removed in 2.0. ' .
            'Use set() instead.'
        );

        if (!$this->contains(urldecode($encodedKey))) {
            $this->pairs[$encodedKey] = $encodedValue;
            $this->init((string)$this);
        }
    }

    /**
     * @deprecated Deprecated since 1.9.18, to be removed in 2.0. Use set($encodedKey, null) instead.
     *
     * @param string $encodedKey
     */
    public function remove($encodedKey)
    {
        @trigger_error(
            'remove() is deprecated since 1.9.18, to be removed in 2.0. ' .
            'Use set($encodedKey, null) instead.'
        );

        $decodedKey = urldecode($encodedKey);

        if ($this->contains($decodedKey)) {
            unset($this->pairs[$decodedKey]);
        }

        $this->init((string)$this);
    }

    /**
     * @param string $encodedKey
     * @param string $encodedValue
     */
    public function set($encodedKey, $encodedValue)
    {
        $decodedKey = urldecode($encodedKey);

        if ($this->contains($decodedKey)) {
            $this->pairs[$decodedKey] = urldecode($encodedValue);
        } else {
            $this->add($encodedKey, $encodedValue);
        }
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

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->pairs());
    }
}
