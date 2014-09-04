<?php

namespace webignition\NormalisedUrl;

class Normaliser {
    
    const DEFAULT_PORT = 80;

    private $knownPorts = array(
        'http' => 80,
        'https' => 443
    );
    
    /**
     * Collection of the different parts of the URL
     * 
     * @var array
     */
    private $parts = array();
    
    
    /**
     *
     * @param array $parts 
     */
    public function __construct($parts) {
        $this->parts = $parts;
        $this->normalise();        
    }
    
    
    /**
     *
     * @return array
     */
    public function getNormalisedParts() {        
        return $this->parts;
    }
    
    private function normalise() {
        $this->normaliseScheme();
        $this->normaliseHost();
        $this->normalisePort();
        $this->normalisePath();
        $this->normaliseQuery();
    }
    
    
    /**
     * Scheme is case-insensitive, normalise to lowercase 
     */
    private function normaliseScheme() {
        if (isset($this->parts['scheme'])) {
            $this->parts['scheme'] = strtolower(trim($this->parts['scheme']));
        }
    }
    
    
    /**
     * Host is case-insensitive, normalise to lowercase and to ascii version of
     * IDN format
     */
    private function normaliseHost() {
        if (isset($this->parts['host'])) {
            $asciiHost = trim(strtolower(\Etechnika\IdnaConvert\IdnaConvert::encodeString($this->parts['host']->get())));            
            $this->parts['host']->set($asciiHost);
        }
    }
    
    
    /**
     * Remove default HTTP port 
     */
    private function normalisePort() {
        if (isset($this->parts['port']) && isset($this->parts['scheme'])) {
            if (isset($this->knownPorts[$this->parts['scheme']]) && $this->knownPorts[$this->parts['scheme']] == $this->parts['port']) {
                unset($this->parts['port']);
            }
        }
    }    
    
    private function normalisePath() {
        if (!isset($this->parts['path'])) {
            $this->parts['path'] = '';
        }
        
        $this->parts['path'] = new \webignition\NormalisedUrl\Path\Path((string)$this->parts['path']);
    }    
    
    private function normaliseQuery() {
        if (isset($this->parts['query'])) {
            $this->parts['query'] = new \webignition\NormalisedUrl\Query\Query((string)$this->parts['query']);
        }
    }
    
}