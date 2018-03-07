<?php

namespace webignition\Tests\NormalisedUrl;

use webignition\NormalisedUrl\Normaliser;
use webignition\Url\UrlInterface;

class NormaliserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider normaliseSchemeDataProvider
     *
     * @param string $url
     * @param string $expectedNormalisedScheme
     */
    public function testNormaliseScheme($url, $expectedNormalisedScheme)
    {
        $normaliser = new Normaliser($url);

        $normalisedParts = $normaliser->getParts();

        $this->assertEquals($expectedNormalisedScheme, $normalisedParts[UrlInterface::PART_SCHEME]);
    }

    /**
     * @return array
     */
    public function normaliseSchemeDataProvider()
    {
        return [
            'http' => [
                'url' => 'http://example.com/',
                'expectedNormalisedScheme' => 'http',
            ],
            'HttP' => [
                'url' => 'HttP://example.com/',
                'expectedNormalisedScheme' => 'http',
            ],
            'HTTP' => [
                'url' => 'HTTP://example.com/',
                'expectedNormalisedScheme' => 'http',
            ],
            'https' => [
                'url' => 'https://example.com/',
                'expectedNormalisedScheme' => 'https',
            ],
            'HttPS' => [
                'url' => 'HttPS://example.com/',
                'expectedNormalisedScheme' => 'https',
            ],
            'HTTPS' => [
                'url' => 'HTTPS://example.com/',
                'expectedNormalisedScheme' => 'https',
            ],
        ];
    }

    /**
     * @dataProvider normaliseHostDataProvider
     *
     * @param string $url
     * @param string $expectedNormalisedHost
     */
    public function testNormaliseHost($url, $expectedNormalisedHost)
    {
        $normaliser = new Normaliser($url);

        $normalisedParts = $normaliser->getParts();

        $this->assertEquals($expectedNormalisedHost, (string)$normalisedParts[UrlInterface::PART_HOST]);
    }

    /**
     * @return array
     */
    public function normaliseHostDataProvider()
    {
        return [
            'is lowercased' => [
                'url' => 'http://exAMPlE.com/',
                'expectedNormalisedHost' => 'example.com',
            ],
            'punycode is unchanged' => [
                'url' => 'http://artesan.xn--a-iga.com/',
                'expectedNormalisedHost' => 'artesan.xn--a-iga.com',
            ],
            'utf8 is converted to punycode' => [
                'url' => 'http://artesan.ía.com/',
                'expectedNormalisedHost' => 'artesan.xn--a-iga.com',
            ],
        ];
    }

    /**
     * @dataProvider normalisePortDataProvider
     *
     * @param string $url
     * @param bool $expectedPortIsSet
     * @param string $expectedNormalisedPort
     */
    public function testNormalisePort($url, $expectedPortIsSet, $expectedNormalisedPort = null)
    {
        $normaliser = new Normaliser($url);

        $normalisedParts = $normaliser->getParts();

        if ($expectedPortIsSet) {
            $this->assertEquals($expectedNormalisedPort, (string)$normalisedParts[UrlInterface::PART_PORT]);
        } else {
            $this->assertArrayNotHasKey(UrlInterface::PART_PORT, $normalisedParts);
        }


    }

    /**
     * @return array
     */
    public function normalisePortDataProvider()
    {
        return [
            'port 80 is removed for http' => [
                'url' => 'http://example.com:80/',
                'expectedPortIsSet' => false,
            ],
            'port 443 is removed for https' => [
                'url' => 'https://example.com:443/',
                'expectedPortIsSet' => false,
            ],
            'port 8080 is not removed' => [
                'url' => 'http://example.com:8080/',
                'expectedPortIsSet' => true,
                'expectedNormalisedPort' => 8080,
            ],
        ];
    }
}
