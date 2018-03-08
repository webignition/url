<?php

namespace webignition\Tests\NormalisedUrl;

use webignition\NormalisedUrl\NormalisedUrl;
use webignition\Tests\DataProvider\HostNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\PathNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\PortNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\QueryNormalisationDataProviderTrait;
use webignition\Tests\DataProvider\SchemeNormalisationDataProviderTrait;

class NormalisedUrlTest extends AbstractNormalisedUrlTest
{
    use SchemeNormalisationDataProviderTrait;
    use HostNormalisationDataProviderTrait;
    use PortNormalisationDataProviderTrait;
    use PathNormalisationDataProviderTrait;
    use QueryNormalisationDataProviderTrait;

    /**
     * @dataProvider idnHostNormalisationDataProvider
     *
     * @param string $url
     * @param bool $enableConvertIdnToUtf8
     * @param string $expectedNormalisedUrl
     */
    public function testIdnHostNormalisation($url, $enableConvertIdnToUtf8, $expectedNormalisedUrl)
    {
        $normalisedUrl = new NormalisedUrl($url);

        if ($enableConvertIdnToUtf8) {
            $normalisedUrl->getConfiguration()->enableConvertIdnToUtf8();
        }

        $this->assertEquals($expectedNormalisedUrl, (string)$normalisedUrl);
    }

    /**
     * @return array
     */
    public function idnHostNormalisationDataProvider()
    {
        $punyCodeUrl = 'http://artesan.xn--a-iga.com/';
        $utf8Url = 'http://artesan.ía.com/';

        return [
            'punycode domain not changed to utf8 with conversion disabled' => [
                'url' => $punyCodeUrl,
                'enableConvertIdnToUtf8' => false,
                'expectedNormalisedUrl' => $punyCodeUrl,
            ],
            'punycode domain is changed to utf8 with conversion enabled' => [
                'url' => $punyCodeUrl,
                'enableConvertIdnToUtf8' => true,
                'expectedNormalisedUrl' => $utf8Url,
            ],
            'utf8 domain normalised to punycode with conversion disabled' => [
                'url' => $utf8Url,
                'enableConvertIdnToUtf8' => false,
                'expectedNormalisedUrl' => $punyCodeUrl,
            ],
            'utf8 domain not normalised to punycode with conversion disabled' => [
                'url' => $utf8Url,
                'enableConvertIdnToUtf8' => true,
                'expectedNormalisedUrl' => $utf8Url,
            ],
        ];
    }

    /**
     * @dataProvider normalisationDataProvider
     *
     * @param string $url
     * @param string $expectedNormalisedUrl
     */
    public function testNormalisation($url, $expectedNormalisedUrl)
    {
        $normalisedUrl = new NormalisedUrl($url);

        $this->assertEquals($expectedNormalisedUrl, (string)$normalisedUrl);
    }

    /**
     * @return array
     */
    public function normalisationDataProvider()
    {
        return array_merge(
            $this->createSchemeNormalisationDataSet(),
            $this->createHostNormalisationDataSet(),
            $this->createPortNormalisationDataSet(),
            $this->createPathNormalisationDataSet(),
            $this->createQueryNormalisationDataSet(),
            [
                'utf8 host normalised to punycode' => [
                    'url' => 'http://artesan.ía.com/',
                    'expectedNormalisedUrl' => 'http://artesan.xn--a-iga.com/',
                ],
            ]
        );
    }

    /**
     * @dataProvider setSchemeDataProvider
     *
     * @param string $url
     * @param string $scheme
     * @param string $expectedScheme
     * @param string $expectedNormalisedUrl
     */
    public function testSetScheme($url, $scheme, $expectedScheme, $expectedNormalisedUrl)
    {
        $normalisedUrl = new NormalisedUrl($url);
        $normalisedUrl->setScheme($scheme);

        $this->assertEquals($expectedScheme, $normalisedUrl->getScheme());
        $this->assertEquals($expectedNormalisedUrl, (string)$normalisedUrl);
    }

    /**
     * @return array
     */
    public function setSchemeDataProvider()
    {
        return [
            'fully qualified url' => [
                'url' => 'https://example.com',
                'scheme' => 'http',
                'expectedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'protocol-relative url' => [
                'url' => '//example.com',
                'scheme' => 'http',
                'expectedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'root-relative url (no host)' => [
                'url' => '/path',
                'scheme' => 'http',
                'expectedScheme' => null,
                'expectedNormalisedUrl' => '/',
            ],
        ];
    }

    /**
     * @dataProvider setHostDataProvider
     *
     * @param string $url
     * @param string $host
     * @param string $expectedHost
     * @param string $expectedNormalisedUrl
     */
    public function testSetHost($url, $host, $expectedHost, $expectedNormalisedUrl)
    {
        $normalisedUrl = new NormalisedUrl($url);
        $normalisedUrl->setHost($host);

        $this->assertEquals($expectedHost, $normalisedUrl->getHost());
        $this->assertEquals($expectedNormalisedUrl, (string)$normalisedUrl);
    }

    /**
     * @return array
     */
    public function setHostDataProvider()
    {
        return [
            'fully qualified url' => [
                'url' => 'http://example.com/',
                'host' => 'FoO.CoM',
                'expectedHost' => 'foo.com',
                'expectedNormalisedUrl' => 'http://foo.com/',
            ],
            'protocol-relative url' => [
                'url' => '//example.com/',
                'host' => 'FoO.CoM',
                'expectedHost' => 'foo.com',
                'expectedNormalisedUrl' => '//foo.com/',
            ],
            'root-relative url (no host)' => [
                'url' => '/path',
                'host' => 'example.com',
                'expectedHost' => 'example.com',
                'expectedNormalisedUrl' => '//example.com/path',
            ],
        ];
    }

    /**
     * @dataProvider setPathDataProvider
     *
     * @param string $url
     * @param string $path
     * @param string $expectedPath
     * @param string $expectedNormalisedUrl
     */
    public function testSetPath($url, $path, $expectedPath, $expectedNormalisedUrl)
    {
        $normalisedUrl = new NormalisedUrl($url);
        $normalisedUrl->setPath($path);

        $this->assertEquals($expectedPath, (string)$normalisedUrl->getPath());
        $this->assertEquals($expectedNormalisedUrl, (string)$normalisedUrl);
    }

    /**
     * @return array
     */
    public function setPathDataProvider()
    {
        return [
            'fully qualified url' => [
                'url' => 'http://example.com/',
                'path' => '/foo/../bar/.',
                'expectedPath' => '/bar',
                'expectedNormalisedUrl' => 'http://example.com/bar',
            ],
            'fully qualified url, no trailing slash' => [
                'url' => 'http://example.com',
                'path' => '/foo/../bar/.',
                'expectedPath' => '/bar',
                'expectedNormalisedUrl' => 'http://example.com/bar',
            ],
            'protocol-relative url' => [
                'url' => '//example.com/',
                'path' => '/foo/../bar/.',
                'expectedPath' => '/bar',
                'expectedNormalisedUrl' => '//example.com/bar',
            ],
            'root-relative url (no host)' => [
                'url' => '/path',
                'path' => '/foo/../bar/.',
                'expectedPath' => '/bar',
                'expectedNormalisedUrl' => '/bar',
            ],
        ];
    }

    /**
     * @dataProvider setQueryDataProvider
     *
     * @param string $url
     * @param string $query
     * @param string $expectedQuery
     * @param string $expectedNormalisedUrl
     */
    public function testSetQuery($url, $query, $expectedQuery, $expectedNormalisedUrl)
    {
        $normalisedUrl = new NormalisedUrl($url);
        $normalisedUrl->setQuery($query);

        $this->assertEquals($expectedQuery, (string)$normalisedUrl->getQuery());
        $this->assertEquals($expectedNormalisedUrl, (string)$normalisedUrl);
    }

    /**
     * @return array
     */
    public function setQueryDataProvider()
    {
        return [
            'no existing query, no fragment, no leading question mark' => [
                'url' => 'http://example.com/',
                'query' => 'key2=value2&key1=value1',
                'expectedQuery' => 'key1=value1&key2=value2',
                'expectedNormalisedUrl' => 'http://example.com/?key1=value1&key2=value2',
            ],
            'no existing query, no fragment, has leading question mark' => [
                'url' => 'http://example.com/',
                'query' => '?key2=value2&key1=value1',
                'expectedQuery' => 'key1=value1&key2=value2',
                'expectedNormalisedUrl' => 'http://example.com/?key1=value1&key2=value2',
            ],
            'no existing query, has fragment, no leading question mark' => [
                'url' => 'http://example.com/#fragment',
                'query' => 'key2=value2&key1=value1',
                'expectedQuery' => 'key1=value1&key2=value2',
                'expectedNormalisedUrl' => 'http://example.com/?key1=value1&key2=value2#fragment',
            ],
            'no existing query, has fragment, has leading question mark' => [
                'url' => 'http://example.com/#fragment',
                'query' => '?key2=value2&key1=value1',
                'expectedQuery' => 'key1=value1&key2=value2',
                'expectedNormalisedUrl' => 'http://example.com/?key1=value1&key2=value2#fragment',
            ],
        ];
    }

    /**
     * @return array
     */
    private function createSchemeNormalisationDataSet()
    {
        $dataSet = [];
        $dataProvider = $this->schemeNormalisationDataProvider();

        foreach ($dataProvider as $index => $testData) {
            $dataSet[$index] = [
                'url' => $testData['url'],
                'expectedNormalisedUrl' => $testData['expectedNormalisedUrl'],
            ];
        }

        return $dataSet;
    }

    /**
     * @return array
     */
    private function createHostNormalisationDataSet()
    {
        $dataSet = [];
        $dataProvider = $this->hostNormalisationDataProvider();

        foreach ($dataProvider as $index => $testData) {
            $dataSet[$index] = [
                'url' => $testData['url'],
                'expectedNormalisedUrl' => $testData['expectedNormalisedUrl'],
            ];
        }

        return $dataSet;
    }

    /**
     * @return array
     */
    private function createPortNormalisationDataSet()
    {
        $dataSet = [];
        $dataProvider = $this->portNormalisationDataProvider();

        foreach ($dataProvider as $index => $testData) {
            $dataSet[$index] = [
                'url' => $testData['url'],
                'expectedNormalisedUrl' => $testData['expectedNormalisedUrl'],
            ];
        }

        return $dataSet;
    }

    /**
     * @return array
     */
    private function createPathNormalisationDataSet()
    {
        $baseUrl = 'http://example.com';

        $dataSet = [];
        $dataProvider = $this->pathNormalisationDataProvider();

        foreach ($dataProvider as $index => $testData) {
            $dataSet[$index] = [
                'url' => $baseUrl . $testData['path'],
                'expectedNormalisedUrl' => $baseUrl . $testData['expectedNormalisedPath'],
            ];
        }

        return $dataSet;
    }

    /**
     * @return array
     */
    private function createQueryNormalisationDataSet()
    {
        $baseUrl = 'http://example.com/';

        $dataSet = [];
        $dataProvider = $this->queryNormalisationDataProvider();

        foreach ($dataProvider as $index => $testData) {
            $expectedNormalisedUrl = $baseUrl;
            $expectedNormalisedQueryString = $testData['expectedNormalisedQueryString'];

            if (!empty($expectedNormalisedQueryString)) {
                $expectedNormalisedUrl .= '?' . $expectedNormalisedQueryString;
            }

            $dataSet[$index] = [
                'url' => $baseUrl . '?' . $testData['queryString'],
                'expectedNormalisedUrl' => $expectedNormalisedUrl,
            ];
        }

        $reservedCharactersQueryDataSet = $this->createReservedCharactersQueryDataSet();

        $dataSet['reserved characters are encoded and capitalised'] = [
            'url' => 'http://example.com/?' . $reservedCharactersQueryDataSet['queryString'],
            'expectedNormalisedUrl' =>
                'http://example.com/?' . $reservedCharactersQueryDataSet['expectedNormalisedQueryString'],
        ];

        return $dataSet;
    }
}
