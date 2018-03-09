<?php

namespace webignition\Tests\Url\Query;

use webignition\Url\Configuration;
use webignition\Url\Query\ParserInterface;
use webignition\Url\Query\Query;

class QueryTest extends AbstractQueryTest
{
    /**
     * @dataProvider setHasConfigurationDataProvider
     *
     * @param Configuration|null $configuration
     * @param bool $expectedHasConfiguration
     */
    public function testSetHasConfiguration($configuration, $expectedHasConfiguration)
    {
        $query = new Query('');

        if ($configuration) {
            $query->setConfiguration($configuration);
        }

        $this->assertEquals($expectedHasConfiguration, $query->hasConfiguration());
    }

    /**
     * @return array
     */
    public function setHasConfigurationDataProvider()
    {
        return [
            'not has configuration' => [
                'configuration' => null,
                'expectedHasConfiguration' => false,
            ],
            'has configuration' => [
                'configuration' => new Configuration(),
                'expectedHasConfiguration' => true,
            ],
        ];
    }

    /**
     * @dataProvider keyValuePairsDataProvider
     *
     * @param string $queryString
     * @param array $expectedKeyValuePairs
     */
    public function testPairs($queryString, array $expectedKeyValuePairs)
    {
        $query = new Query($queryString);

        $this->assertEquals($expectedKeyValuePairs, $query->pairs());
    }

    /**
     * @dataProvider containsDataProvider
     *
     * @param string $queryString
     * @param string $key
     * @param bool $expectedContains
     */
    public function testContains($queryString, $key, $expectedContains)
    {
        $query = new Query($queryString);

        $this->assertEquals($expectedContains, $query->contains($key));
    }

    /**
     * @return array
     */
    public function containsDataProvider()
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
     * @dataProvider addDataProvider
     *
     * @param string $queryString
     * @param string $key
     * @param mixed $value
     * @param array $expectedPairs
     */
    public function testAdd($queryString, $key, $value, $expectedPairs)
    {
        $query = new Query($queryString);
        $query->add($key, $value);

        $this->assertEquals($expectedPairs, $query->pairs());
    }

    /**
     * @return array
     */
    public function addDataProvider()
    {
        return [
            'add to empty query string' => [
                'queryString' => '',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                ],
            ],
            'key not present' => [
                'queryString' => 'b=2',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            'key present; un-encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 1,
                ],
            ],
            'key present; encoded' => [
                'queryString' => 'a%2Fa=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 1,
                ],
            ],
            'key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a%2Fa',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider removeDataProvider
     *
     * @param string $queryString
     * @param string $key
     * @param array $expectedPairs
     */
    public function testRemove($queryString, $key, $expectedPairs)
    {
        $query = new Query($queryString);
        $query->remove($key);

        $this->assertEquals($expectedPairs, $query->pairs());
    }

    /**
     * @return array
     */
    public function removeDataProvider()
    {
        return [
            'remove from empty query string' => [
                'queryString' => '',
                'key' => 'a',
                'expectedPairs' => [],
            ],
            'key not present' => [
                'queryString' => 'a=1',
                'key' => 'b',
                'expectedPairs' => [
                    'a' => 1,
                ],
            ],
            'key present; un-encoded' => [
                'queryString' => 'a/a=1&b=2',
                'key' => 'a/a',
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
            'key present; encoded' => [
                'queryString' => 'a%2Fa=1&b=2',
                'key' => 'a/a',
                'expectedPairs' => [
                    'b' => 2,
                ],
            ],
            'key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1&b=2',
                'key' => 'a%2Fa',
                'expectedPairs' => [
                    'b' => 2,
                ],
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
    public function testSet($queryString, $key, $value, $expectedPairs)
    {
        $query = new Query($queryString);
        $query->set($key, $value);

        $this->assertEquals($expectedPairs, $query->pairs());
    }

    /**
     * @return array
     */
    public function setDataProvider()
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
            'key not present' => [
                'queryString' => 'b=2',
                'key' => 'a',
                'value' => 1,
                'expectedPairs' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            'key present; un-encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'key present; encoded' => [
                'queryString' => 'a%2Fa=1',
                'key' => 'a/a',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
                ],
            ],
            'key present; existing key un-encoded; addition key encoded' => [
                'queryString' => 'a/a=1',
                'key' => 'a%2Fa',
                'value' => 2,
                'expectedPairs' => [
                    'a/a' => 2,
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
}
