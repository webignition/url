<?php

namespace webignition\Tests\NormalisedUrl;

abstract class AbstractNormalisedUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function schemeNormalisationDataProvider()
    {
        return [
            'http' => [
                'url' => 'http://example.com/',
                'expectedNormalisedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'HttP' => [
                'url' => 'HttP://example.com/',
                'expectedNormalisedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'HTTP' => [
                'url' => 'HTTP://example.com/',
                'expectedNormalisedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'https' => [
                'url' => 'https://example.com/',
                'expectedNormalisedScheme' => 'https',
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
            'HttPS' => [
                'url' => 'HttPS://example.com/',
                'expectedNormalisedScheme' => 'https',
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
            'HTTPS' => [
                'url' => 'HTTPS://example.com/',
                'expectedNormalisedScheme' => 'https',
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
        ];
    }

    /**
     * @return array
     */
    public function hostNormalisationDataProvider()
    {
        return [
            'is lowercased' => [
                'url' => 'http://exAMPlE.com/',
                'expectedNormalisedHost' => 'example.com',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'punycode is unchanged' => [
                'url' => 'http://artesan.xn--a-iga.com/',
                'expectedNormalisedHost' => 'artesan.xn--a-iga.com',
                'expectedNormalisedUrl' => 'http://artesan.xn--a-iga.com/',
            ],
            'utf8 is converted to punycode' => [
                'url' => 'http://artesan.ía.com/',
                'expectedNormalisedHost' => 'artesan.xn--a-iga.com',
                'expectedNormalisedUrl' => 'http://artesan.xn--a-iga.com/',
            ],
        ];
    }

    /**
     * @return array
     */
    public function portNormalisationDataProvider()
    {
        return [
            'port 80 is removed for http' => [
                'url' => 'http://example.com:80/',
                'expectedPortIsSet' => false,
                'expectedNormalisedPort' => null,
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'port 443 is removed for https' => [
                'url' => 'https://example.com:443/',
                'expectedPortIsSet' => false,
                'expectedNormalisedPort' => null,
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
            'port 8080 is not removed' => [
                'url' => 'http://example.com:8080/',
                'expectedPortIsSet' => true,
                'expectedNormalisedPort' => 8080,
                'expectedNormalisedUrl' => 'http://example.com:8080/',
            ],
        ];
    }

    /**
     * @return array
     */
    public function pathNormalisationDataProvider()
    {
        return [
            'null path' => [
                'path' => null,
                'expectedNormalisedPath' => '/',
            ],
            'empty path' => [
                'path' => '',
                'expectedNormalisedPath' => '/',
            ],
            'slash' => [
                'path' => '/',
                'expectedNormalisedPath' => '/',
            ],
            'single dot' => [
                'path' => '.',
                'expectedNormalisedPath' => '/',
            ],
            'slash single dot' => [
                'path' => '/.',
                'expectedNormalisedPath' => '/',
            ],
            'double dot' => [
                'path' => '..',
                'expectedNormalisedPath' => '/',
            ],
            'slash double dot' => [
                'path' => '/..',
                'expectedNormalisedPath' => '/',
            ],
            'rfc3986 5.2.4 example 1' => [
                'path' => '/a/b/c/./../../g',
                'expectedNormalisedPath' => '/a/g',
            ],
            'rfc3986 5.2.4 example 2' => [
                'path' => '/mid/content=5/../6',
                'expectedNormalisedPath' => '/mid/6',
            ],
            'many single dot' => [
                'path' => '/./././././././././././././././',
                'expectedNormalisedPath' => '/',
            ],
            'many double dot' => [
                'path' => '/../../../../../../',
                'expectedNormalisedPath' => '/',
            ],
            'double trailing slash' => [
                'path' => '//',
                'expectedNormalisedPath' => '/',
            ],
            'triple trailing slash' => [
                'path' => '///',
                'expectedNormalisedPath' => '/',
            ],
            'non-empty path with double trailing slash' => [
                'path' => '/one/two//',
                'expectedNormalisedPath' => '/one/two/',
            ],
            'non-empty path with triple trailing slash' => [
                'path' => '/one/two///',
                'expectedNormalisedPath' => '/one/two/',
            ],
            'non-empty path with leading double shash mid double slash and trailing double slash' => [
                'path' => '//one//two//',
                'expectedNormalisedPath' => '//one//two/',
            ],
            'non-empty path with leading triple slash mid triple slash and trailing triple slash' => [
                'path' => '///one///two///',
                'expectedNormalisedPath' => '///one///two/',
            ],
            'non-empty path with double mid slash and no trailing slash' => [
                'path' => '/one//two',
                'expectedNormalisedPath' => '/one//two',
            ],
        ];
    }

    /**
     * @return array
     */
    public function queryNormalisationDataProvider()
    {
        return [
            'null' => [
                'queryString' => null,
                'expectedNormalisedQueryString' => '',
            ],
            'empty' => [
                'queryString' => '',
                'expectedNormalisedQueryString' => '',
            ],
            'sort alphabetically by key' => [
                'queryString' => 'a=1&c=3&b=2',
                'expectedNormalisedQueryString' => 'a=1&b=2&c=3',
            ],
            'key without value' => [
                'queryString' => 'key2&key1=value1',
                'expectedNormalisedQueryString' => 'key1=value1&key2',
            ],
            'encoded unreserved characters are decoded' => $this->createUnreservedCharactersQueryDataSet(),
        ];
    }

    /**
     * @return array
     */
    private function createUnreservedCharactersQueryDataSet()
    {
        $alpha = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseAlpha = strtoupper($alpha);
        $digit = '0123456789';
        $otherUnreservedCharacters = '-._~';

        $unreservedCharacters = str_split($alpha.$uppercaseAlpha.$digit.$otherUnreservedCharacters);

        $keyValues = [];

        $keyIndex = 0;
        foreach ($unreservedCharacters as $unreservedCharacter) {
            $keyValues['key' . $unreservedCharacter] = $unreservedCharacter;
            $keyIndex++;
        }

        ksort($keyValues);

        $encodedKeyValuePairs = [];
        $decodedKeyValuePairs = [];

        foreach ($keyValues as $key => $value) {
            $encodedKeyValuePairs[] = $key.'=%' . dechex(ord($value));
            $decodedKeyValuePairs[] = $key.'=' . (string)$value;
        }

        return [
            'queryString' => implode('&', $encodedKeyValuePairs),
            'expectedNormalisedQueryString' => implode('&', $decodedKeyValuePairs),
        ];
    }

    /**
     * @return array
     */
    protected function createReservedCharactersQueryDataSet()
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
