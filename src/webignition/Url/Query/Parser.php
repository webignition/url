<?php

namespace webignition\Url\Query;

class Parser {
    
    const PAIR_DELIMITER = '&';
    const KEY_VALUE_DELIMITER = '=';
   
    /**
     * Supplied URL, unmodified
     * 
     * @var string
     */
    private $origin = null; 
    
    
    /**
     *
     * @var array
     */
    private $pairs = null;
    
    
    /**
     *
     * @param string $url 
     */
    public function __construct($url) {
        $this->origin = $url;      
    }
    
    
    /**
     *
     * @return array
     */
    public function getPairs() {        
        if (is_null($this->pairs)) {            
            $this->parse();            
            $this->normalise();
        }
        
        return $this->pairs;
    }    
    
        
    private function parse() {
        $pairStrings = explode(self::PAIR_DELIMITER, $this->origin);
        foreach ($pairStrings as $pairString) {            
            $currentPair = explode(self::KEY_VALUE_DELIMITER, $pairString);
            
            $key = urldecode($currentPair[0]);
            $value = isset($currentPair[1]) ? urldecode($currentPair[1]) : null;
            
            $this->pairs[$key] = $value;            
        }
    }
    
    private function normalise() {         
        ksort($this->pairs);
    }
    
}