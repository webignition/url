<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testCreate($queryString, $expectedNormalisedQueryString)
    {
        $normalisedQuery = new Query($queryString);
        $this->assertEquals($expectedNormalisedQueryString, (string)$normalisedQuery);
    }

    public function createDataProvider()
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
        ];
    }
}
