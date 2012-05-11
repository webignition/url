<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

abstract class AbstractUrlTest extends PHPUnit_Framework_TestCase {
    
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';
    const USER = 'user';
    const PASS = 'pass';    
    const HOST = 'www.example.com'; 
    const PORT_REGULAR =  80;
    const PORT_SECURE = 443;
    const PATH_PART_ONE = 'firstPathPart';
    const PATH_PART_TWO = 'secondPathPart';
    const PATH_PART_THREE = 'lastPathPart';
    const PATH_FILENAME = 'example.html';
    const QUERY_KEY_1 = 'query-key-1[]';
    const QUERY_VALUE_1 = 'query-value-1';
    const QUERY_KEY_2 = 'query-key-2';
    const QUERY_VALUE_2 = 'query-value-2';
    const QUERY_KEY_3 = 'query-key-%3c3%3e';
    const QUERY_VALUE_3 = 'query+value+3';
    const FRAGMENT = 'fragment';
    
    const PATH_PART_DELIMITER = '/';
    
    private $inputAndExpectedOutputUrls = array();
    
    protected $urls = array();
    
    public function __construct() {
        $this->urls = $this->urls();
    }    

    
    /**
     *
     * @param array $inputAndExpectedOutputUrls 
     */  
    protected function setInputAndExpectedOutputUrls($inputAndExpectedOutputUrls) {
        $this->inputAndExpectedOutputUrls = $inputAndExpectedOutputUrls;
    }
    
    protected function runInputToExpectedOutputTests() {             
        foreach ($this->inputAndExpectedOutputUrls as $inputUrl => $expectedOutputUrl) {
            $url = new \webignition\Url\Url($inputUrl);            
            $this->assertEquals($expectedOutputUrl, (string)$url);            
        }         
    }
    
    /**
     *
     * @return array
     */
    private function urls() {
        return array(
            'complete' => $this->completeUrl(),
            'normalised-complete' => $this->normalisedCompleteUrl(),
            'protocol-agnostic-regular' => $this->protocolRelativeRegularUrl(),
            'relative-root-url' => $this->relativeRootUrl(),
            'relative-url' => $this->relativeUrl(),
            'no-scheme' => '://'.self::USER.':'.self::PASS.'@'.self::HOST.':'.self::PORT_REGULAR.'/firstPathPart/lastPathPart/?key1=value1&key2=value2#fragment',
            'no-host' => self::SCHEME_HTTP.'://'.self::USER.':'.self::PASS.'@:'.self::PORT_REGULAR.'/firstPathPart/lastPathPart/?key1=value1&key2=value2#fragment'
        );
    } 
    
    
    /**
     *
     * @return string
     */
    private function completeUrl() {
        return  self::SCHEME_HTTP
                .'://'
                .self::USER
                .':'
                .self::PASS
                .'@'
                .self::HOST
                .':'
                .self::PORT_REGULAR
                .$this->completeUrlPath()
                .'?'
                .$this->completeUrlQueryString()
                .'#fragment';
    }
 
    
    /**
     *
     * @return string
     */
    private function normalisedCompleteUrl() {
        return  self::SCHEME_HTTP
                .'://'
                .self::USER
                .':'
                .self::PASS
                .'@'
                .self::HOST
                .$this->completeUrlPath()
                .'?'
                .$this->sortedCompleteUrlQueryString()
                .'#fragment';
    }    
    
    /**
     *
     * @return string
     */
    protected function completeUrlPath() {
        return self::PATH_PART_DELIMITER.implode(self::PATH_PART_DELIMITER, array(
            self::PATH_PART_ONE,
            self::PATH_PART_TWO,
            self::PATH_PART_THREE
        )).self::PATH_PART_DELIMITER.self::PATH_FILENAME;
    }
    
    /**
     *
     * @return string
     */
    protected function completeUrlQueryString() {
        $queryStringPairs = array(
            urlencode(self::QUERY_KEY_1).'='.urlencode(self::QUERY_VALUE_1),
            urlencode(self::QUERY_KEY_2).'='.urlencode(self::QUERY_VALUE_2),
            urlencode(self::QUERY_KEY_3).'='.urlencode(self::QUERY_VALUE_3)            
        );
        
        return implode('&', $queryStringPairs);
    }
    
    /**
     *
     * @return string
     */
    protected function sortedCompleteUrlQueryString() {
        $queryPairs = array(
            urlencode(self::QUERY_KEY_1) => urlencode(self::QUERY_VALUE_1),
            urlencode(self::QUERY_KEY_2) => urlencode(self::QUERY_VALUE_2),
            urlencode(self::QUERY_KEY_3) => urlencode(self::QUERY_VALUE_3)              
        );
        
        ksort($queryPairs);
        
        $queryStringPairs = array();
        
        foreach ($queryPairs as $key => $value) {
            $queryStringPairs[] = $key.'='.$value;
        }
        
        return implode('&', $queryStringPairs);
    }    
    
    /**
     *
     * @return string
     */
    protected function protocolRelativeRegularUrl() {
        return '//'.self::HOST.$this->completeUrlPath();
    }
    
    /**
     *
     * @return string
     */
    protected function relativeRootUrl() {
        return $this->completeUrlPath();
    }
    
    /**
     *
     * @return string
     */
    protected function relativeUrl() {
        return substr($this->completeUrlPath(), 1);
    }
    
}