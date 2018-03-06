<?php

namespace webignition\Url\Query;

class Query {
    
    /**
     * Supplied URL, unmodified query string
     * 
     * @var string
     */
    private $origin = null;
    
    
    /**
     *
     * @var \webignition\Url\Query\Parser
     */
    private $parser = null;
    
    
    /**
     * Collection of key=value pairs
     *
     * @var array
     */
    private $pairs = null;
    
    
    /**
     *
     * @var \webignition\Url\Parser\Configuration
     */
    private $configuration = null;    
    
    
    /**
     * 
     * @param string $encodedQueryString 
     */
    public function __construct($encodedQueryString) { 
        $this->setOrigin($encodedQueryString);
    }
    
    
    /**
     *
     * @return string
     */
    public function __toString() {
        return str_replace(array('%7E'), array('~'), $this->buildQueryStringFromPairs());
    }
    
    
    /**
     * 
     * @return string
     */
    private function buildQueryStringFromPairs() {
        $encoder = new Encoder($this->pairs(), $this->configuration);
        return (string)$encoder;
    }    
    
    
    /**
     *
     * @return array
     */
    public function pairs() {        
        if (is_null($this->pairs)) {
            $this->pairs = $this->getParser()->getKeyValuePairs();
        }
        
        return $this->pairs;
    }
   
    
    /**
     *
     * @return \webignition\Url\Query\Parser 
     */
    public function getParser() {
        if (is_null($this->parser)) {
            $this->parser = new \webignition\Url\Query\Parser($this->getOrigin());
        }
        
        return $this->parser;
    }   
    
    
    /**
     *
     * @return string
     */
    protected function getOrigin() {
        return $this->origin;
    }
    
    
    /**
     *
     * @param string $key
     * @return boolean
     */
    public function contains($key) {
        return array_key_exists($key, $this->pairs());
    }
    
    
    /**
     *
     * @param string $origin 
     */
    private function setOrigin($origin) {
        $this->origin = $origin;
    }
    
    
    
    protected function reset() {
        $this->pairs = null;
        $this->parser = null;
    }
    
    
    /**
     *
     * @param string $encodedKey
     * @param string $encodedValue 
     */
    public function add($encodedKey, $encodedValue) {        
        if (!$this->contains(urldecode($encodedKey))) {
            $this->reset();
            $this->setOrigin($this->getOrigin() . '&' . $encodedKey . '=' . $encodedValue);            
        }
    }
    
    
    /**
     *
     * @param string $encodedKey 
     */
    public function remove($encodedKey) {
        if ($this->contains(urldecode($encodedKey))) {
            unset($this->pairs[urldecode($encodedKey)]);
            $this->setOrigin((string)$this);
        }
        
        $this->reset();
    }
    
    
    /**
     *
     * @param string $encodedKey
     * @param string $encodedValue 
     */
    public function set($encodedKey, $encodedValue) {
        if ($this->contains(urldecode($encodedKey))) {
            $this->pairs[urldecode($encodedKey)] = urldecode($encodedValue);
        } else {
            $this->add($encodedKey, $encodedValue);
        }
    }
    
    
    /**
     * 
     * @param \webignition\Url\Configuration $configuration
     */
    public function setConfiguration(\webignition\Url\Configuration $configuration) {
        $this->configuration = $configuration;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function hasConfiguration() {
        return !is_null($this->configuration);
    }    
    
}