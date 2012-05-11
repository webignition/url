<?php

namespace webignition\Url;

class NormalisedUrl extends Url {    
    
    /**
     *
     * @var \webignition\Url\Normaliser
     */
    private $normaliser = null;    
    
    
    /**
     *
     * @var array
     */
    private $normalisedParts = null;
    
    
    /**
     *
     * @return boolean
     */
    public function hasScheme() {        
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }    
    
    /**
     *
     * @return string|null
     */
    public function getScheme() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }    
    
    /**
     *
     * @return boolean
     */
    public function hasHost() {       
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }
    
    /**
     *
     * @return string|null
     */
    public function getHost() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }
    

    /**
     *
     * @return boolean
     */
    public function hasPort() {
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }
    
    
    /**
     *
     * @return int|null
     */
    public function getPort() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasUser() {
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }
    
    /**
     *
     * @return string|null
     */
    public function getUser() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }    
    
    /**
     *
     * @return boolean
     */
    public function hasPass() {
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }
    
    /**
     *
     * @return string|null
     */
    public function getPass() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }
    
    /**
     *
     * @return boolean
     */
    public function hasPath() {
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }
    
    /**
     *
     * @return string|null
     */
    public function getPath() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }    
    
    
    /**
     *
     * @return boolean
     */
    public function hasQuery() {
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    }
    
    /**
     *
     * @return string|null
     */
    public function getQuery() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }    
    
    /**
     *
     * @return boolean
     */
    public function hasFragment() {
        return $this->hasPart(strtolower(str_replace('has', '', __FUNCTION__)));
    } 
    
    /**
     *
     * @return string|null
     */
    public function getFragment() {
        return $this->getPart(strtolower(str_replace('get', '', __FUNCTION__)));
    }      
  
    
    /**
     *
     * @param string $partName
     * @return boolean
     */
    private function hasPart($partName) {       
        return array_key_exists($partName, $this->normalisedParts());
    }
    
    
    /**
     *
     * @return mixed
     */
    private function getPart($partName) {        
        return $this->hasPart($partName) ? $this->normalisedParts[$partName] : null;
    }    
    
    /**
     *
     * @return array
     */
    public function normalisedParts() {
        if (is_null($this->normalisedParts)) {
            $this->normalisedParts = $this->normaliser($this->parts())->getNormalisedParts();
        }       
        
        return $this->normalisedParts;
    }    
    
    /**
     *
     * @return \webignition\Url\Normaliser 
     */
    private function normaliser() {
        if (is_null($this->normaliser)) {
            $this->normaliser = new \webignition\Url\Normaliser($this->parts());
        }
        
        return $this->normaliser;
    }    
    
}