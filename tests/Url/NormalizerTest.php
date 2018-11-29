<?php

namespace webignition\Tests\Url;

use webignition\Url\Normalizer;
use webignition\Url\NormalizerOptions;
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
            'forceHttp: http url' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'forceHttp: https url' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'forceHttps: http url' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'forceHttps: https url' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'forceHttp and forceHttps: http url' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'forceHttp and forceHttps: https url' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'removeUserInfo=false: no user info' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => false,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'removeUserInfo=false: has user info' => [
                'url' => new Url('https://user:password@example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => false,
                ],
                'expectedUrl' => new Url('https://user:password@example.com'),
            ],
            'removeUserInfo=true: no user info' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'removeUserInfo=true: has user info' => [
                'url' => new Url('https://user:password@example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
        ];
    }
}
