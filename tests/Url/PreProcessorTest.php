<?php

namespace webignition\Tests\Url;

use webignition\Url\PreProcessor;

class PreProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider preProcessDataProvider
     *
     * @param string|null $url
     * @param string $expectedPreProcessedUrl
     */
    public function testPreProcess(?string $url, string $expectedPreProcessedUrl)
    {
        $this->assertEquals($expectedPreProcessedUrl, PreProcessor::preProcess($url));
    }

    public function preProcessDataProvider(): array
    {
        return [
            'null' => [
                'url' => null,
                'expectedPreProcessedUrl' => '',
            ],
            'empty' => [
                'url' => '',
                'expectedPreProcessedUrl' => '',
            ],
            'trailing tab' => [
                'url' => "http://example.com/page/\t",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'trailing newline' => [
                'url' => "http://example.com/page/\n",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'trailing line return' => [
                'url' => "http://example.com/page/\r",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'leading tab' => [
                'url' => "\thttp://example.com/page/",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'leading newline' => [
                'url' => "\nhttp://example.com/page/",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'leading line return' => [
                'url' => "\rhttp://example.com/page/",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'tab in path' => [
                'url' => "http://example.com/\tpage/",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'newline in path' => [
                'url' => "http://example.com/\npage/",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'line return in path' => [
                'url' => "http://example.com/\rpage/",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
            'many tabs, newlines and line returns' => [
                'url' => "\n\thttp://example.com\r\n/\rpage/\t",
                'expectedPreProcessedUrl' => 'http://example.com/page/',
            ],
        ];
    }
}
