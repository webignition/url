<?php

namespace webignition\Tests\Url\Query;

use phpmock\mockery\PHPMockery;
use webignition\Url\Configuration;
use webignition\Url\Query\Encoder;

/**
 * Covers verifying that query string keys can be minimally-encoded if needed
 * Arose from the need to preserve the ?, + and / characters
 * in 'http://s1.wp.com/_static/??-eJyNUdFuwyAM/KExd2vVtQ/TvoWAA7QmRmAU9e9H0kmNVjXKEz50Z+4OGJMyPAgOArGqRNWFoQCFKxa4oCRtrmpG76aUN1jQO2L3ELBzaLmK6pmIRxiDdShPosUbGUkLWpW4yD+0Jovp2K5j0jKPvhlc5U8LVU86ZChyI3ziisfYwqbagfFseCvNMHEuYLHXlWSrKgzmTlVjajHWC5oqbqODxlrArXG9zpP473zlzR/AEXea1tbev7PMRhyzzajtXPtP/P7Yn06H8+789Xn5BWIC3X4='
 */
class EncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        PHPMockery::mock(
            'webignition\Url\Query',
            'md5'
        )->andReturnValues(
            [1, 'foo']
        );
    }

    /**
     * @dataProvider setHasConfigurationDataProvider
     *
     * @param Configuration|null $configuration
     * @param bool $expectedHasConfiguration
     */
    public function testSetHasConfiguration($configuration, $expectedHasConfiguration)
    {
        $encoder = new Encoder([]);

        if ($configuration) {
            $encoder->setConfiguration($configuration);
        }

        $this->assertEquals($expectedHasConfiguration, $encoder->hasConfiguration());
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
     * @dataProvider encodeDataProvider
     *
     * @param array $pairs
     * @param Configuration|null $configuration
     * @param string $expectedEncodedQueryString
     */
    public function testEncode($pairs, $configuration, $expectedEncodedQueryString)
    {
        $encoder = new Encoder($pairs);

        if ($configuration) {
            $encoder->setConfiguration($configuration);
        }

        $this->assertEquals($expectedEncodedQueryString, (string)$encoder);
    }

    /**
     * @return array
     */
    public function encodeDataProvider()
    {
        $disableFullEncodingConfiguration = new Configuration();
        $disableFullEncodingConfiguration->disableFullyEncodeQueryStringKeys();

        return [
            'no keys need encoding' => [
                'pairs' => [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ],
                'configuration' => null,
                'expectedEncodedQueryString' => 'a=1&b=2&c=3',
            ],
            'no keys need encoding; contains null values' => [
                'pairs' => [
                    'a' => 1,
                    'b' => null,
                    'c' => 3,
                ],
                'configuration' => null,
                'expectedEncodedQueryString' => 'a=1&b&c=3',
            ],
            'special characters, full encoding' => [
                'pairs' => [
                    'a/a' => 1,
                    'b?b' => 2,
                    'c!c' => 3,
                ],
                'configuration' => null,
                'expectedEncodedQueryString' => 'a%2Fa=1&b%3Fb=2&c%21c=3',
            ],
            'special characters, minimal encoding' => [
                'pairs' => [
                    'a/a' => 1,
                    'b?b' => 2,
                    'c!c' => 3,
                ],
                'configuration' => $disableFullEncodingConfiguration,
                'expectedEncodedQueryString' => 'a/a=1&b?b=2&c!c=3',
            ],
            'special and very special characters, full encoding' => [
                'pairs' => [
                    'a%23a' => 1,
                    'b%26b' => 2,
                    'c!c' => 3,
                ],
                'configuration' => null,
                'expectedEncodedQueryString' => 'a%2523a=1&b%2526b=2&c%21c=3',
            ],
            'special and very special characters, minimal encoding' => [
                'pairs' => [
                    'a%23a' => 1,
                    'b%26b' => 2,
                    'c!c' => 3,
                ],
                'configuration' => $disableFullEncodingConfiguration,
                'expectedEncodedQueryString' => 'a%23a=1&b%26b=2&c!c=3',
            ],
        ];
    }

    public function testEncodeNullValues()
    {
        $encoder = new Encoder([
            'a' => 1,
            'b' => null,
            'c' => 3,
        ]);

        $this->assertEquals('a=1&b&c=3', (string)$encoder);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}