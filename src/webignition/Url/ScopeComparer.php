<?php
namespace webignition\Url;

class ScopeComparer {
    
    
    /**
     *
     * @var Url
     */
    private $sourceUrl;
    
    /**
     *
     * @var Url
     */    
    private $comparatorUrl;
    
    private $sourceUrlString;
    private $comparatorUrlString;
    
    
    private $ignoredParts = array(
        'port',
        'user',
        'pass',
        'query',
        'fragment'
    );
    
    
    private $equivalentSchemes = array();    
    private $equivalentHosts = array();
    
    
    /**
     * 
     * @param array $schemes
     */
    public function addEquivalentSchemes($schemes) {
        $this->equivalentSchemes[] = $schemes;
    }
    
    
    /**
     * 
     * @param array $subdomains
     */
    public function addEquivalentHosts($hosts) {
        $this->equivalentHosts[] = $hosts;
    }    
   
    
    /**
     * Is the given comparator url in the scope
     * of this url?
     * 
     * Comparator is in the same scope as the source if:
     *  - scheme is the same or equivalent (e.g. http and https are equivlent)
     *  - hostname is the same or equivalent (equivalency looks at subdomain equivalence e.g. example.com and www.example.com
     *  - path is the same or greater (e.g. sourcepath = /one/two, comparatorpath = /one/two or /one/two/*
     * 
     * Comparison ignores:
     *  - port
     *  - user
     *  - pass
     *  - query
     *  - fragment
     * 
     * @param \webignition\Url\Url $sourceUrl
     * @param \webignition\Url\Url $comparatorUrl
     * @return boolean
     */
    public function isInScope(Url $sourceUrl, Url $comparatorUrl) {
        $this->sourceUrl = clone $sourceUrl;
        $this->comparatorUrl = clone $comparatorUrl;
        
        foreach ($this->ignoredParts as $partName) {
            $this->sourceUrl->setPart($partName, null);
        }
        
        $this->sourceUrlString = (string)$this->sourceUrl;
        $this->comparatorUrlString = (string)$this->comparatorUrl;
        
        if ((string)$this->sourceUrl == (string)$this->comparatorUrl) {
            return true;
        }
        
        if ($this->isSourceUrlSubtringOfComparatorUrl()) {
            return true;
        }
        
        if (!$this->areSchemesEquivalent()) {
            return false;
        }
        
        if (!$this->areHostsEquivalent()) {
            return false;
        }
        
        return $this->isSourcePathSubtringOfComparatorPath();        
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function isSourceUrlSubtringOfComparatorUrl() {
        return substr($this->comparatorUrlString, 0, strlen($this->sourceUrlString)) == $this->sourceUrlString;
    }    
    
    
    /**
     * 
     * @return boolean
     */
    private function areSchemesEquivalent() {
        if ($this->sourceUrl->getScheme() === $this->comparatorUrl->getScheme()) {
            return true;
        }
        
        foreach ($this->equivalentSchemes as $equivalentSchemeSet) {
            if (in_array($this->sourceUrl->getScheme(), $equivalentSchemeSet) && in_array($this->comparatorUrl->getScheme(), $equivalentSchemeSet)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function areHostsEquivalent() {        
        if ((string)$this->sourceUrl->getHost() === (string)$this->comparatorUrl->getHost()) {
            return true;
        }
        
        foreach ($this->equivalentHosts as $equivalentHostSet) {                        
            if (in_array($this->sourceUrl->getHost(), $equivalentHostSet) && in_array($this->comparatorUrl->getHost(), $equivalentHostSet)) {
                return true;
            }
        }
        
        return false;        
    }
    
    
    /**
     *
     * @return boolean 
     */
    private function isSourcePathSubtringOfComparatorPath() {        
        if (!$this->sourceUrl->hasPath() && $this->comparatorUrl->hasPath()) {
            return true;
        }
        
        $sourcePath = (string)$this->sourceUrl->getPath();
        $comparatorpath = (string)$this->comparatorUrl->getPath();
        
        if ($sourcePath == $comparatorpath) {
            return true;
        }
        
        return substr($comparatorpath, 0, strlen($sourcePath)) == $sourcePath;
    }
}