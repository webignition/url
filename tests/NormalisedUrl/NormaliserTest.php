<?php

namespace webignition\Tests\NormalisedUrl;

use webignition\NormalisedUrl\Normaliser;
use webignition\Url\UrlInterface;

class NormaliserTest extends AbstractNormalisedUrlTest
{
    /**
     * @dataProvider schemeNormalisationDataProvider
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
     * @dataProvider hostNormalisationDataProvider
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
     * @dataProvider portNormalisationDataProvider
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
            $this->assertEquals($expectedNormalisedPort, $normalisedParts[UrlInterface::PART_PORT]);
        } else {
            $this->assertArrayNotHasKey(UrlInterface::PART_PORT, $normalisedParts);
        }
    }

    /**
     * @dataProvider pathNormalisationDataProvider
     *
     * @param string $path
     * @param string $expectedNormalisedPath
     */
    public function testNormalisePath($path, $expectedNormalisedPath)
    {
        $normaliser = new Normaliser('http://example.com' . $path);

        $normalisedParts = $normaliser->getParts();

        $this->assertEquals($expectedNormalisedPath, (string)$normalisedParts[UrlInterface::PART_PATH]);
    }

    /**
     * @dataProvider queryNormalisationDataProvider
     *
     * @param string $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testNormaliseQuery($queryString, $expectedNormalisedQueryString)
    {
        $normaliser = new Normaliser('http://example.com/?' . $queryString);

        $normalisedParts = $normaliser->getParts();

        if (isset($normalisedParts[UrlInterface::PART_QUERY])) {
            $this->assertEquals($expectedNormalisedQueryString, (string)$normalisedParts[UrlInterface::PART_QUERY]);
        } else {
            $this->assertArrayNotHasKey(UrlInterface::PART_QUERY, $normalisedParts);
        }
    }
}
