<?php

namespace webignition\Url;

class Configuration {
    
    
    /**
     * Whether to fully url-encode query string keys
     * Default is true
     * 
     * When false, query string keys will be minimally encoded
     * At the very least, must encode: # &
     * 
     * @var boolean
     */
    private $fullyEncodeQueryStringKeys = true;
    
    
    public function enableFullyEncodeQueryStringKeys() {
        $this->fullyEncodeQueryStringKeys = true;
    }
    
    public function disableFullyEncodeQueryStringKeys() {
        $this->fullyEncodeQueryStringKeys = false;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function getFullyEncodeQueryStringKeys() {
        return $this->fullyEncodeQueryStringKeys;
    }
    
}