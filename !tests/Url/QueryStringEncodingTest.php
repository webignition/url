<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;


/**
 * Test that query string keys can be minimally-encoded if needed
 * Arose from the need to preserve the ?, + and / characters
 * in 'http://s1.wp.com/_static/??-eJyNUdFuwyAM/KExd2vVtQ/TvoWAA7QmRmAU9e9H0kmNVjXKEz50Z+4OGJMyPAgOArGqRNWFoQCFKxa4oCRtrmpG76aUN1jQO2L3ELBzaLmK6pmIRxiDdShPosUbGUkLWpW4yD+0Jovp2K5j0jKPvhlc5U8LVU86ZChyI3ziisfYwqbagfFseCvNMHEuYLHXlWSrKgzmTlVjajHWC5oqbqODxlrArXG9zpP473zlzR/AEXea1tbev7PMRhyzzajtXPtP/P7Yn06H8+789Xn5BWIC3X4='
 * 
 */
class QueryStringEncodingTest extends AbstractRegularUrlTest {      
    
    public function testMinimalEncodingWithNoEncodableKeys() {
        $query = 'a=1&b=2&c=3';
        
        $url = new \webignition\Url\Url('http://example.com/?' . $query);
        $url->getConfiguration()->disableFullyEncodeQueryStringKeys();
        
        $this->assertEquals($query, (string)$url->getQuery());
    }      
    
    
    public function testMinimalEncodingWithEncodableKeysWithNoVerySpecialCharacters() {
        $query = 'a/a=1&b?b=2&c!c=3';
        
        $url = new \webignition\Url\Url('http://example.com/?' . $query);
        $url->getConfiguration()->disableFullyEncodeQueryStringKeys();
        
        $this->assertEquals($query, (string)$url->getQuery());
    }
    
    
    public function testMinimalEncodingWithEncodableKeysContainingVerySpecialCharacters() {
        $query = 'a%23a=1&b%26b=2&c!c=3';
        
        $url = new \webignition\Url\Url('http://example.com/?' . $query);
        $url->getConfiguration()->disableFullyEncodeQueryStringKeys();
        
        $this->assertEquals($query, (string)$url->getQuery());
    }  
    
}