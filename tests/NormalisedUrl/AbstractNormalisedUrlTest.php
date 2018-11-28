<?php

namespace webignition\Tests\NormalisedUrl;

abstract class AbstractNormalisedUrlTest extends \PHPUnit_Framework_TestCase
{
    protected function createReservedCharactersQueryDataSet(): array
    {
        $reservedCharacters = ['!','*',"'",'(',')',';',':','@','&','=','+','$',',','/','?','#','[',']'];

        $encodedKeyValuePairs = [];
        $lowercaseEncodedKeyValuePairs = [];

        $keyIndex = 0;

        foreach ($reservedCharacters as $reservedCharacter) {
            $key = 'key' . $keyIndex;

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

        return [
            'queryString' => $lowercasePercentEncodedQueryString,
            'expectedNormalisedQueryString' => $percentEncodedQueryString,
        ];
    }
}
