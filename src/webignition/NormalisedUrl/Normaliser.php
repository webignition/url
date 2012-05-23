<?php

namespace webignition\Url;

class Normaliser {
    
    const DEFAULT_PORT = 80;
    
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
     * Host is case-insensitive, normalise to lowercase 
     */
    private function normaliseHost() {
        if (isset($this->parts['host'])) {
            $this->parts['host'] = strtolower(trim($this->parts['host']));
        }
    }
    
    
    /**
     * Remove default HTTP port 
     */
    private function normalisePort() {
        if (isset($this->parts['port']) && $this->parts['port'] == self::DEFAULT_PORT) {
            unset($this->parts['port']);
        }
    }
    
    
    private function normalisePath() {
        $this->removeDotSegments();
        $this->addTrailingSlash();
        $this->addLeadingSlash();
    }
    
    /**
     * Directories are indicated with a trailing slash and should be included in URLs
     * Append trailing slash to path if not present 
     */    
    private function addTrailingSlash() {
        if (!isset($this->parts['path'])) {
            $this->parts['path'] = '';
        }
        
        if ($this->parts['path'] == '' || $this->parts['path'] == '/') {
            return $this->parts['path'] = '/';
        }
        
        $pathParts = explode('/', $this->parts['path']);
        $lastPathPart = $pathParts[count($pathParts) - 1];

        if (substr_count($lastPathPart, '.')) {
            return;
        }
        
        $lastPathCharacter = substr($this->parts['path'], strlen($this->parts['path']) - 1);
        if ($lastPathCharacter != '/') {
            $this->parts['path'] .= '/';
        }        
    }
    
    
    /**
     * Prepend path with leading slash if this URL has a host and the path lacks
     * the leading slash 
     */
    private function addLeadingSlash() {
        $firstPathCharacter = substr($this->parts['path'], 0, 1);
        
        if (isset($this->parts['host']) && $firstPathCharacter != '/') {
            $this->parts['path'] = '/' . $this->parts['path'];
        }
    }
    
    
    /**
     * Remove the special "." and ".." complete path segments from a referenced path
     * 
     * Uses algorithm as defined in rfc3968#5.2.4
     * @see http://tools.ietf.org/html/rfc3986#section-5.2.4 
     */
    private function removeDotSegments() {
        if (!isset($this->parts['path'])) {
            $this->parts['path'] = '';
        }
        
        if ($this->parts['path'] == '/') {
            return;
        }
        
        $dotOnlyPaths = array('/..', '/.');
        foreach ($dotOnlyPaths as $dotOnlyPath) {
            if ($this->parts['path'] == $dotOnlyPath) {
                return $this->parts['path'] = '/'; 
            }            
        }        
        
        $pathParts = explode('/', $this->parts['path']);        
        $comparisonPathParts = $pathParts;        
        
        $normalisedPathParts = array();
        
        foreach ($comparisonPathParts as $pathPart) {            
            if ($pathPart == '.') {
                continue;
            }
               
            if ($pathPart == '..') {
                array_pop($normalisedPathParts);
            } else {
                $normalisedPathParts[] = $pathPart;
            }
        }
        
        $this->parts['path'] = implode('/', $normalisedPathParts);        
    }   
    
    
}