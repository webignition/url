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
     * @param string $encodedQueryString 
     */
    public function __construct($encodedQueryString) {        
        $this->origin = trim($encodedQueryString);
    }
    
    
    /**
     *
     * @return string
     */
    public function __toString() {        
        return str_replace(array('%7E'), array('~'), http_build_query($this->pairs()));
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
    
}