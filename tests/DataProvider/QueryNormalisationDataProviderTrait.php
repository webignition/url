<?php

namespace webignition\Tests\DataProvider;

trait QueryNormalisationDataProviderTrait
{
    public function queryNormalisationDataProvider(): array
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
            'expectedNormalisedQueryString' => implode('&', $decodedKeyValuePairs),
        ];
    }
}
