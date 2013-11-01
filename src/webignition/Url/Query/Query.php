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
     * @var string
     */
    private $nullValuePlaceholder = null;
    
    
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
        $pairs = $this->pairs();
        foreach ($pairs as $key => $value) {
            if (is_null($value)) {
                $pairs[$key] = $this->getNullValuePlaceholder();
            }
        }        
        
        return str_replace('=' . $this->getNullValuePlaceholder(), '', http_build_query($pairs));
    }
    
    
    
    /**
     * 
     * @return string
     */
    private function getNullValuePlaceholder() {
        if (is_null($this->nullValuePlaceholder)) {
            $placeholder = $this->generateNullValuePlaceholder();            
            $values = array_values($this->pairs());            
            while (in_array($placeholder, $values)) {
                $placeholder = $this->generateNullValuePlaceholder();
            }
            
            $this->nullValuePlaceholder = $placeholder;
        }
        
        return $this->nullValuePlaceholder;
    }
    
    
    /**
     * 
     * @return string
     */
    private function generateNullValuePlaceholder() {
        return md5(time());
    }
    
    
    
    
    
    /**
     *
     * @return array
     */
    public function pairs() {        
        if (is_null($this->pairs)) {
            $this->pairs = $this->parser()->getKeyValuePairs();
        }
        
        return $this->pairs;
    }
   
    
    /**
     *
     * @return \webignition\Url\Query\Parser 
     */
    protected function parser() {
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
    
}