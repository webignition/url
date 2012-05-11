<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that normalisation capitalises percent-encoded entities
 *  
 */
class PercentEncodingCapitalisationTest extends AbstractUrlTest {   
    
    public function testNormalisedUrlCapitalisesPercentEncodedEntities() {      
        $reservedCharacters = array('!','*',"'",'(',')',';',':','@','&','=','+','$',',','/','?','#','[',']');
            
        $encodedKeyValuePairs = array();
        $lowercaseEncodedKeyValuePairs = array();
        
        $keyIndex = 0;
        
        foreach  ($reservedCharacters as $reservedCharacter) {
            $key = 'key'.$keyIndex;
            
            $encodedKeyValuePairs[$key] = urlencode($reservedCharacter);
            $lowercaseEncodedKeyValuePairs[$key] = strtolower(urlencode($reservedCharacter));
            
            $keyIndex++;
        }
        
        ksort($encodedKeyValuePairs);        
        ksort($lowercaseEncodedKeyValuePairs);
        
        $percentEncodedQueryString = '';
        $lowercasePercentEncodedQueryString = '';
        
        foreach ($encodedKeyValuePairs as $key => $value) {
            $percentEncodedQueryString .= '&' . urlencode($key).'='.$value;
        }
        
        foreach ($lowercaseEncodedKeyValuePairs as $key => $value) {
            $lowercasePercentEncodedQueryString .= '&' . urlencode($key).'='.$value;
        }
        
        $percentEncodedQueryString = substr($percentEncodedQueryString, 1);
        $lowercasePercentEncodedQueryString = substr($lowercasePercentEncodedQueryString, 1);
        
        $url = new \webignition\Url\Url(self::SCHEME_HTTP.'://'.self::HOST.'/?'.$lowercasePercentEncodedQueryString);
        $this->assertEquals($percentEncodedQueryString, (string)$url->getQuery());
    } 
}