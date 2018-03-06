<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Normaliser;

class NormaliserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $queryString
     * @param array $expectedNormalisedKeyValuePairs
     */
    public function testCreate($queryString, array $expectedNormalisedKeyValuePairs)
    {
        $normaliser = new Normaliser($queryString);
        $normalisedKeyValuePairs = $normaliser->getKeyValuePairs();

        $this->assertEquals($expectedNormalisedKeyValuePairs, $normalisedKeyValuePairs);
    }

    public function createDataProvider()
    {
        return [
            'null' => [
                'queryString' => null,
                'expectedNormalisedKeyValuePairs' => [],
            ],
            'empty' => [
                'queryString' => '',
                'expectedNormalisedKeyValuePairs' => [],
            ],
            'sort alphabetically by key' => [
                'queryString' => 'a=1&c=3&b=2',
                'expectedNormalisedKeyValuePairs' => [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ],
            ],
            'key without value' => [
                'queryString' => 'key2&key1=value1',
                'expectedNormalisedKeyValuePairs' => [
                    'key1' => 'value1',
                    'key2' => null,
                ],
            ],
        ];
    }
}
