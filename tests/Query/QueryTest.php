<?php

namespace webignition\Url\Tests\Query;

use webignition\Url\Query\Query;

class QueryTest extends AbstractQueryTest
{
    /**
     * @dataProvider keyValuePairsDataProvider
     *
     * @param string|null $queryString
     * @param array $expectedKeyValuePairs
     */
    public function testPairs(?string $queryString, array $expectedKeyValuePairs)
    {
        $query = new Query($queryString);

        $this->assertEquals($expectedKeyValuePairs, $query->pairs());
    }

    /**
     * @dataProvider containsDataProvider
     *
     * @param string|null $queryString
     * @param string|null $key
     * @param bool $expectedContains
     */
    public function testContains(?string $queryString, ?string $key, bool $expectedContains)
    {
        $query = new Query($queryString);

        $this->assertEquals($expectedContains, $query->contains($key));
    }

    public function containsDataProvider(): array
    {
        return [
            'null query, null key' => [
                'queryString' => null,
                'key' => null,
                'expectedContains' => false,
            ],
            'empty query, empty key' => [
                'queryString' => '',
                'key' => '',
                'expectedContains' => false,
            ],
            'a=1 does not contain b' => [
                'queryString' => 'a=1',
                'key' => 'b',
                'expectedContains' => false,
            ],
            'a=1 does contain a' => [
                'queryString' => 'a=1',
                'key' => 'a',
                'expectedContains' => true,
            ],
            '%3F=1 does contain ?' => [
                'queryString' => '%3F=1',
                'key' => '?',
                'expectedContains' => true,
            ],
        ];
    }

    /**
     * @dataProvider setDataProvider
     *
     * @param string $queryString
     * @param string $key
     * @param mixed $value
     * @param array $expectedPairs
     */
    public function testSet(string $queryString, string $key, $value, array $expectedPairs)
    {
        $query = new Query($queryString);
        $query->set($key, $value);

        $this->assertEquals($expectedPairs, $query->pairs());
    }

    public function setDataProvider(): array
    {
        return [
            'set on empty query string' => [
                'queryString' => '',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                ],
            ],
            'add: key not present' => [
                'queryString' => 'b=2',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            'add: key present; un-encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'add: key present; encoded' => [
                'queryString' => 'a%2Fa=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'add: key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a%2Fa',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'remove from empty query string' => [
                'queryString' => '',
                'key' => 'a',
                'value'=> null,
                'expectedPairs' => [],
            ],
            'remove: key not present' => [
                'queryString' => 'a=1',
                'key' => 'b',
                'value'=> null,
                'expectedPairs' => [
                    'a' => 1,
                ],
            ],
            'remove: key present; un-encoded' => [
                'queryString' => 'a/a=1&b=2',
                'key' => 'a/a',
                'value'=> null,
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
            'remove: key present; encoded' => [
                'queryString' => 'a%2Fa=1&b=2',
                'key' => 'a/a',
                'value'=> null,
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
            'remove: key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1&b=2',
                'key' => 'a%2Fa',
                'value'=> null,
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
        ];
    }

    public function testCreateEmptyQuery()
    {
        $query = new Query();

        $this->assertEquals([], $query->pairs());
        $this->assertEquals('', (string)$query);
    }

    /**
     * @dataProvider toStringDataProvider
     *
     * @param string $queryString
     * @param string $expectedQueryString
     */
    public function testToString(string $queryString, string $expectedQueryString)
    {
        $query = new Query($queryString);

        $this->assertEquals((string) $query, $expectedQueryString);
    }

    public function toStringDataProvider(): array
    {
        return [
            'default' => [
                'queryString' => 'foo=bar',
                'expectedQueryString' => 'foo=bar',
            ],
            'reserved characters are encoded and capitalised' => $this->createReservedCharactersQueryDataSet(),
            'encoded unreserved characters are decoded' => $this->createUnreservedCharactersQueryDataSet(),
        ];
    }

    private function createReservedCharactersQueryDataSet(): array
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
            'expectedQueryString' => $percentEncodedQueryString,
        ];
    }

    private function createUnreservedCharactersQueryDataSet(): array
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
            'expectedQueryString' => implode('&', $decodedKeyValuePairs),
        ];
    }
}
