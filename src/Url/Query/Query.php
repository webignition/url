<?php

namespace webignition\Url\Query;

use webignition\Url\Configuration;

class Query
{
    /**
     * Supplied URL, unmodified query string
     *
     * @var string
     */
    protected $origin = null;

    /**
     * @var ParserInterface
     */
    protected $parser = null;

    /**
     * Collection of key=value pairs
     *
     * @var array
     */
    private $pairs = null;

    /**
     * @var Configuration
     */
    private $configuration = null;

    /**
     * @param string $encodedQueryString
     */
    public function __construct($encodedQueryString)
    {
        $this->setOrigin($encodedQueryString);
        $this->parser = new Parser($this->origin);
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
        if (is_null($this->pairs)) {
            $this->pairs = $this->getParser()->getKeyValuePairs();
        }

        return $this->pairs;
    }

    /**
     * @return ParserInterface
     */
    public function getParser()
    {
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
     * @param string $origin
     */
    private function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    protected function reset()
    {
        $this->pairs = null;
        $this->parser = new Parser($this->origin);
    }

    /**
     * @param string $encodedKey
     * @param string $encodedValue
     */
    public function add($encodedKey, $encodedValue)
    {
        if (!$this->contains(urldecode($encodedKey))) {
            $addition = $encodedKey . '=' . $encodedValue;

            if (!empty($this->origin)) {
                $addition = '&' . $addition;
            }

            $this->setOrigin($this->origin . $addition);
            $this->reset();
        }
    }

    /**
     * @param string $encodedKey
     */
    public function remove($encodedKey)
    {
        $decodedKey = urldecode($encodedKey);

        if ($this->contains($decodedKey)) {
            unset($this->pairs[$decodedKey]);
            $this->setOrigin((string)$this);
        }

        $this->reset();
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
}
