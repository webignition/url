<?php

namespace webignition\NormalisedUrl\Query;

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
    private $normaliser = null;
    
    
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
    private function parser() {
        if (is_null($this->normaliser)) {
            $this->normaliser = new \webignition\NormalisedUrl\Query\Normaliser($this->origin);
        }
        
        return $this->normaliser;
    }   
    
}