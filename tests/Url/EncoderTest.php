<?php

namespace webignition\Tests\Url;

use webignition\Tests\DataProvider\PathEncoderDataProviderTrait;
use webignition\Url\Encoder;
use webignition\Url\Url;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    use PathEncoderDataProviderTrait;
    /**
     * @dataProvider encodeDataProvider
     *
     * @param Url $url
     * @param string $expectedEncodedUrl
     */
    public function testEncode(Url $url, $expectedEncodedUrl)
    {
        $this->assertEquals($expectedEncodedUrl, (string)Encoder::encode($url));
    }

    /**
     * @return array
     */
    public function encodeDataProvider()
    {
        return array_merge(
            [
                'no path' => [
                    'url' => new Url('http://example.com'),
                    'expectedEncodedUrl' => 'http://example.com',
                ],
                'scheme and host only remains unchanged' => [
                    'url' => new Url('http://example.com/'),
                    'expectedEncodedUrl' => 'http://example.com/',
                ],
                'single space in path' => [
                    'url' => new Url('http://example.com/foo bar/'),
                    'expectedEncodedUrl' => 'http://example.com/foo%20bar/',
                ],
                'multiple spaces in path' => [
                    'url' => new Url('http://example.com/foo1 bar1/foo2 bar2/foo3 bar3/'),
                    'expectedEncodedUrl' => 'http://example.com/foo1%20bar1/foo2%20bar2/foo3%20bar3/',
                ],
                'single percent in path' => [
                    'url' => new Url('http://example.com/foo%bar/'),
                    'expectedEncodedUrl' => 'http://example.com/foo%25bar/',
                ],
                'multiple percents in path' => [
                    'url' => new Url('http://example.com/foo1%bar1/foo2%bar2/foo3%bar3/'),
                    'expectedEncodedUrl' => 'http://example.com/foo1%25bar1/foo2%25bar2/foo3%25bar3/',
                ],
            ],
            $this->createPathEncoderDataSet()
        );
    }

    private function createPathEncoderDataSet()
    {
        $dataSet = [];
        $dataProvider = $this->pathEncoderDataProvider();

        foreach ($dataProvider as $index => $testData) {
            $dataSet[$index . '; path only'] = [
                'url' => new Url($testData['path']),
                'expectedEncodedUrl' => $testData['expectedEncodedPath'],
            ];

            $dataSet[$index . '; scheme, host and path'] = [
                'url' => new Url('http://example.com' . $testData['path']),
                'expectedEncodedUrl' => 'http://example.com' . $testData['expectedEncodedPath'],
            ];
        }

        return $dataSet;
    }
}
