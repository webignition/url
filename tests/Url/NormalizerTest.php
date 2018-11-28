<?php

namespace webignition\Tests\Url;

use IpUtils\Exception\InvalidExpressionException;
use webignition\Url\Normalizer;
use webignition\Url\NormalizerOptions;
use webignition\Url\Query\Query;
use webignition\Url\Url;
use webignition\Url\UrlInterface;

class NormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Normalizer
     */
    private $normalizer;

    protected function setUp()
    {
        parent::setUp();

        $this->normalizer = new Normalizer();
    }

    /**
     * @dataProvider normalizeDataProvider
     *
     * @param UrlInterface $url
     * @param array $options
     * @param UrlInterface $expectedUrl
     */
    public function testNormalize(UrlInterface $url, array $options, UrlInterface $expectedUrl)
    {
        $normalizedUrl = $this->normalizer->normalize($url, $options);

        $this->assertEquals((string) $expectedUrl, (string) $normalizedUrl);
    }

    public function normalizeDataProvider(): array
    {
        return [
            'no scheme, no default scheme' => [
                'url' => new Url('example.com/foo/bar'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/foo/bar'),
            ],
            'no scheme (protocol-relative), no default scheme' => [
                'url' => new Url('//example.com/foo/bar'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/foo/bar'),
            ],
            'no scheme (protocol-relative), normalizeScheme=false' => [
                'url' => new Url('//example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_NORMALIZE_SCHEME => false,
                ],
                'expectedUrl' => new Url('//example.com/foo/bar'),
            ],
            'no scheme, has default scheme' => [
                'url' => new Url('example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_DEFAULT_SCHEME => 'foo-scheme'
                ],
                'expectedUrl' => new Url('foo-scheme://example.com/foo/bar'),
            ],
            'non-lowercase scheme' => [
                'url' => new Url('HTTP://example.com/foo/bar'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/foo/bar'),
            ],
        ];
    }
}
