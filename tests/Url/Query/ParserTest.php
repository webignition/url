<?php

namespace webignition\Tests\Url\Query;

use phpmock\mockery\PHPMockery;
use webignition\Url\Configuration;
use webignition\Url\Query\Encoder;
use webignition\Url\Query\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getKeyValuePairsDataProvider
     *
     * @param string $queryString
     * @param array $expectedKeyValuePairs
     */
    public function testGetKeyValuePairs($queryString, array $expectedKeyValuePairs)
    {
        $parser = new Parser($queryString);

        $this->assertEquals($expectedKeyValuePairs, $parser->getKeyValuePairs());
    }

    /**
     * @return array
     */
    public function getKeyValuePairsDataProvider()
    {
        return [
            'null' => [
                'queryString' => null,
                'expectedKeyValuePairs' => [],
            ],
            'empty' => [
                'queryString' => '',
                'expectedKeyValuePairs' => [],
            ],
            'un-encoded' => [
                'queryString' => 'a=1&b=2&c=3',
                'expectedKeyValuePairs' => [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ],
            ],
            'un-encoded; null value' => [
                'queryString' => 'a=1&b&c=3',
                'expectedKeyValuePairs' => [
                    'a' => 1,
                    'b' => null,
                    'c' => 3,
                ],
            ],
            'encoded values' => [
                'queryString' => 'a=%26&b=%21&c=%3F',
                'expectedKeyValuePairs' => [
                    'a' => '&',
                    'b' => '!',
                    'c' => '?',
                ],
            ],
            'encoded keys' => [
                'queryString' => 'a%26a=1&b%21b=2&c%3Fc=3',
                'expectedKeyValuePairs' => [
                    'a&a' => '1',
                    'b!b' => '2',
                    'c?c' => '3',
                ],
            ],
            'encoded keys and encoded values' => [
                'queryString' => 'a%26a=%26&b%21b=%21&c%3Fc=%3F',
                'expectedKeyValuePairs' => [
                    'a&a' => '&',
                    'b!b' => '!',
                    'c?c' => '?',
                ],
            ],
        ];
    }
}
