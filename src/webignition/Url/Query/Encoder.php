<?php

namespace webignition\Url\Query;

class Encoder {
    
    const PAIR_DELIMITER = '&';
    const KEY_VALUE_DELIMITER = '=';
    const FRAGMENT_IDENTIFIER = '#';
    
    
    /**
     * Collection of characters that must be included if only minimally-encoding
     * query string keys
     * 
     * @var string[]
     */
    private $verySpecialCharacters = array(
        self::PAIR_DELIMITER => '%26',
        self::FRAGMENT_IDENTIFIER => '%23'
    );
    
    
    
    private $pairs = array();
    
    
    /**
     *
     * @var \webignition\Url\Configuration 
     */
    private $configuration;
    

    /**
     *
     * @var string
     */
    private $nullValuePlaceholder = null;    
    
    
    /**
     * 
     * @param array $pairs
     */
    public function __construct($pairs, \webignition\Url\Configuration $configuration = null) { 
        $this->pairs = $pairs;
        $this->configuration = $configuration;
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
        foreach ($this->pairs as $key => $value) {
            if (is_null($value)) {
                $this->pairs[$key] = $this->getNullValuePlaceholder();
            }
        }
        
        $baseEncodedQuery = str_replace('=' . $this->getNullValuePlaceholder(), '', http_build_query($this->pairs));
        
        
        if ($this->hasConfiguration() && !$this->configuration->getFullyEncodeQueryStringKeys()) {
            $keyValuePairs = explode(self::PAIR_DELIMITER, $baseEncodedQuery);
            
            foreach ($keyValuePairs as $keyValuePairIndex => $keyValuePair) {
                $keyAndValue = explode(self::KEY_VALUE_DELIMITER, $keyValuePair);
                
                $keyAndValue[0] = str_replace(array_keys($this->verySpecialCharacters), array_values($this->verySpecialCharacters), rawurldecode($keyAndValue[0]));
                
                //$keyAndValue[0] = str_replace(array_keys($this->verySpecialCharacters), array_values($this->verySpecialCharacters), rawurldecode($keyAndValue[0]));                
                $keyValuePairs[$keyValuePairIndex] = implode('=', $keyAndValue);
            }
            
            $baseEncodedQuery = implode(self::PAIR_DELIMITER, $keyValuePairs);
        }
        
        return $baseEncodedQuery;
    }
    
    
    
    /**
     * 
     * @return string
     */
    private function getNullValuePlaceholder() {
        if (is_null($this->nullValuePlaceholder)) {
            $placeholder = $this->generateNullValuePlaceholder();            
            $values = array_values($this->pairs);            
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