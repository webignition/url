<?php

namespace webignition\NormalisedUrl\Path;

class Normaliser {
   
    /**
     * 
     * @var string
     */
    private $path = null;     
    
    /**
     *
     * @param string $path 
     */
    public function __construct($path) {
        $this->path = (string)$path;      
        $this->normalise();
    }    
    
    /**
     *
     * @return string
     */
    public function get() {
        return $this->path;
    }
    
    private function normalise() {
        $this->removeDotSegments();
        $this->reduceMultipleTrailingSlashes();
        $this->addTrailingSlash();      
    }
    
    /**
     * Directories are indicated with a trailing slash and should be included in URLs
     * Append trailing slash to path if not present 
     */    
    private function addTrailingSlash() {
        if (!isset($this->path)) {
            $this->path = '';
        }
        
        if ($this->path == '' || $this->path == '/') {
            $this->path = '/';
        }
        
        return;     
    }
    
    
    /**
     * Remove the special "." and ".." complete path segments from a referenced path
     * 
     * Uses algorithm as defined in rfc3968#5.2.4
     * @see http://tools.ietf.org/html/rfc3986#section-5.2.4 
     */
    private function removeDotSegments() {        
        if ($this->path == '/') {
            return;
        }
        
        $dotOnlyPaths = array('/..', '/.');
        foreach ($dotOnlyPaths as $dotOnlyPath) {
            if ($this->path == $dotOnlyPath) {
                return $this->path = '/'; 
            }            
        }        
        
        $pathParts = explode('/', $this->path);        
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
        
        $this->path = implode('/', $normalisedPathParts);        
    }
    
    
    private function reduceMultipleTrailingSlashes() {
        if (!substr_count($this->path, '//')) {
            return;
        }
        
        $lastCharacter = substr($this->path, strlen($this->path) - 1);
        if ($lastCharacter != '/')  {
            return;
        }
        
        $trailingSlashSegmentIndex = strlen($this->path) - 1;
        $hasReachedNonSlashCharacter = false;
        
        for ($characterIndex = strlen($this->path) - 1; $characterIndex >= 0; $characterIndex--) {
            if (!$hasReachedNonSlashCharacter) {
                if ($this->path[$characterIndex] == '/') {
                    $trailingSlashSegmentIndex = $characterIndex;
                } else {
                    $hasReachedNonSlashCharacter = true;
                }                
            }
        }
     
        $trailingSlashSegment = ($trailingSlashSegmentIndex == 0) ? $this->path : substr($this->path, $trailingSlashSegmentIndex);
        
        while (substr_count($trailingSlashSegment, '//')) {
            $trailingSlashSegment = str_replace('//', '/', $trailingSlashSegment);
        }
        
        $this->path = substr($this->path, 0, $trailingSlashSegmentIndex) . $trailingSlashSegment;
    }
    
}