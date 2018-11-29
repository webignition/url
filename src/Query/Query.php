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

    public function __construct(?string $encodedQueryString = '')
    {
        $this->init($encodedQueryString);
    }

    protected function init(?string $encodedQueryString)
    {
        $this->parser = $this->createParser($encodedQueryString);
        $this->pairs = $this->parser->getKeyValuePairs();
    }

    protected function createParser(?string $encodedQueryString): ParserInterface
    {
        return new Parser($encodedQueryString);
    }

    public function __toString(): string
    {
        return str_replace(array('%7E'), array('~'), $this->buildQueryStringFromPairs());
    }

    private function buildQueryStringFromPairs(): string
    {
        $encoder = new Encoder($this->pairs(), $this->configuration);
        return (string)$encoder;
    }

    public function pairs(): array
    {
        return $this->pairs;
    }

    public function contains(?string $key): bool
    {
        return array_key_exists($key, $this->pairs());
    }

    public function set(?string $encodedKey, ?string $encodedValue)
    {
        $decodedKey = urldecode($encodedKey);

        if (is_null($encodedValue)) {
            unset($this->pairs[$decodedKey]);
        } else {
            $this->pairs[$decodedKey] = urldecode($encodedValue);
        }

        $this->init((string)$this);
    }

    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function isEmpty(): bool
    {
        return empty($this->pairs());
    }
}
