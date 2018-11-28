<?php

namespace webignition\Tests\NormalisedUrl;

use webignition\NormalisedUrl\Normaliser;
use webignition\Tests\DataProvider\HostNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\PathNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\PortNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\QueryNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\SchemeNormalisationDataProviderTrait;
use webignition\Url\UrlInterface;

class NormaliserTest extends AbstractNormalisedUrlTest
{
    use SchemeNormalisationDataProviderTrait;
    use HostNormalisationDataProviderTrait;
    use PortNormalisationDataProviderTrait;
    use PathNormalisationDataProviderTrait;
    use QueryNormalisationDataProviderTrait;

    /**
     * @dataProvider schemeNormalisationDataProvider
     *
     * @param string $url
     * @param string $expectedNormalisedScheme
     */
    public function testNormaliseScheme(string $url, string $expectedNormalisedScheme)
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
    public function testNormaliseHost(string $url, string $expectedNormalisedHost)
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
     * @param string|null $expectedNormalisedPort
     */
    public function testNormalisePort(string $url, bool $expectedPortIsSet, ?string $expectedNormalisedPort = null)
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
     * @param string|null $path
     * @param string $expectedNormalisedPath
     */
    public function testNormalisePath(?string $path, string $expectedNormalisedPath)
    {
        $normaliser = new Normaliser('http://example.com' . $path);

        $normalisedParts = $normaliser->getParts();

        $this->assertEquals($expectedNormalisedPath, (string)$normalisedParts[UrlInterface::PART_PATH]);
    }

    /**
     * @dataProvider queryNormalisationDataProvider
     *
     * @param string|null $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testNormaliseQuery(?string $queryString, string $expectedNormalisedQueryString)
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
