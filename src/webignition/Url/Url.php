<?php
namespace webignition\Url;

class Url {
    
    /**
     *
     * @var \webignition\Url\Parser
     */
    private $parser = null;
    
    /**
     *
     * @var \webignition\Url\Normaliser
     */
    private $normaliser = null;
    
    /**
     * Original unmodified source URL
     * 
     * @var string
     */
    private $originUrl = '';
    
    /**
     * Component parts of this URL, with keys:
     * -scheme
     * -host
     * -port
     * -user
     * -pass
     * -path
     * -query - after the question mark ?
     * -fragment - after the hashmark #
     * 
     * 
     * @var array
     */
    private $parts = null;    
    
    
    /**
     * Normalised equivalents of $this->parts
     * 
     * @var array
     */
    private $normalisedParts = null;
    
    
    /**
     *
     * @param string $originUrl 
     */
    public function __construct($originUrl) {
        $this->originUrl = $originUrl;
    }
    
    
    /**
     *
     * @param string $name
     * @param array $arguments
     * @return mixed 
     */
    public function __call($name, $arguments) {        
        $methodGroups = array(
            'hasNormalised',
            'getNormalised',
            'has',
            'get'
        );
        
        foreach ($methodGroups as $methodGroup) {
            if  (substr($name, 0, strlen($methodGroup)) == $methodGroup) {
                $methodName = $methodGroup.'Part';                
                return $this->$methodName(strtolower(str_replace($methodGroup, '', $name)));
            }            
        }       
    }
    
    
    public function __toString() {
/**
 *
     * -scheme
     * -host
     * -port
     * -user
     * -pass
     * -path
     * -query - after the question mark ?
     * -fragment - after the hashmark #
 *  
 */        
        
        $url = '';
        
        if ($this->hasScheme()) {
            $url .= $this->getScheme() . ':';
        }
        
        if ($this->hasHost()) {
            $url .= '//';
            
            if ($this->hasCredentials()) {
                $url .= $this->getCredentials() . '@';
            }            
            
            $url .= $this->getHost();
        }        
        
        if ($this->hasNormalisedPort()) {
            $url .= ':' . $this->getNormalisedPort();
        }
        
        $url .= $this->getPath();        
        
        if ($this->hasQuery()) {
            $url .= '?' . $this->getQuery();
        }
        
        if ($this->hasFragment()) {
            $url .= '#' . $this->getFragment();
        }
        
        return $url;        
    }
    
    
    /**
     *
     * @return boolean
     */
    private function hasCredentials() {
        return $this->hasUser();
    }
    
    
    /**
     *
     * @return string 
     */
    private function getCredentials() {
        $credentials = '';
        
        if ($this->hasUser()) {
            $credentials .= $this->getUser() . ':';
        }
        
        if ($this->hasPass()) {
            $credentials .= $this->getPass();
        }
        
        return $credentials;
    }
    
    
    /**
     *
     * @return string
     */
    public function getScheme() {        
        return $this->getNormalisedScheme();
    }
    
    
    /**
     *
     * @return string
     */
    public function getHost() {
        return $this->getNormalisedHost();
    }
    

    /**
     *
     * @return string
     */
    public function getPath() {        
        return $this->getNormalisedPath();
    }    
    
    
    /**
     *
     * @param string $partName
     * @return mixed
     */
    private function getPart($partName) {
        $parts = $this->parts();
        return (isset($parts[$partName])) ? $parts[$partName] : null;
    }    

    
    /**
     *
     * @param string $partName
     * @return mixed
     */
    private function getNormalisedPart($partName) {
        $normalisedParts = $this->normalisedParts();
        return (isset($normalisedParts[$partName])) ? $normalisedParts[$partName] : null;
    }    
    
    /**
     *
     * @param string $partName
     * @return boolean
     */
    private function hasPart($partName) {        
        return !is_null($this->getPart($partName));
    }
    

    /**
     *
     * @param string $partName
     * @return boolean
     */
    private function hasNormalisedPart($partName) {
        return !is_null($this->getNormalisedPart($partName));
    }    
    
    
    /**
     *
     * @return array
     */
    private function parts() {
        if (is_null($this->parts)) {
            $this->parts = $this->parser()->getParts();
        }       
        
        return $this->parts;
    }
    
    
    /**
     *
     * @return array
     */
    private function normalisedParts() {
        if (is_null($this->normalisedParts)) {
            $this->normalisedParts = $this->normaliser()->getNormalisedParts();
        }
        
        return $this->normalisedParts;
    }
    
    
    /**
     *
     * @return \webignition\Url\Parser
     */
    private function parser() {
        if (is_null($this->parser)) {
            $this->parser = new \webignition\Url\Parser($this->originUrl);
        }
        
        return $this->parser;
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