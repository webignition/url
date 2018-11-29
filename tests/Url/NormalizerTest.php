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
     * @dataProvider schemeNormalizationDataProvider
     * @dataProvider forceHttpForceHttpsDataProvider
     * @dataProvider removeUserInfoDataProvider
     * @dataProvider hostNormalizationDataProvider
     * @dataProvider removeFragmentDataProvider
     * @dataProvider removeWwwDataProvider
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

    public function schemeNormalizationDataProvider(): array
    {
        return [
            'setDefaultSchemeIfNoScheme=false, no scheme' => [
                'url' => new Url('example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => false,
                ],
                'expectedUrl' => new Url('example.com/foo/bar'),
            ],
            'setDefaultSchemeIfNoScheme=false, no scheme, protocol-relative' => [
                'url' => new Url('//example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => false,
                ],
                'expectedUrl' => new Url('//example.com/foo/bar'),
            ],
            'setDefaultSchemeIfNoScheme=true, no scheme' => [
                'url' => new Url('example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => true,
                ],
                'expectedUrl' => new Url('http://example.com/foo/bar'),
            ],
            'setDefaultSchemeIfNoScheme=true, no scheme, protocol-relative' => [
                'url' => new Url('//example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => true,
                ],
                'expectedUrl' => new Url('http://example.com/foo/bar'),
            ],
        ];
    }

    public function forceHttpForceHttpsDataProvider(): array
    {
        return [
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
        ];
    }

    public function removeUserInfoDataProvider(): array
    {
        return [
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

    public function hostNormalizationDataProvider(): array
    {
        return [
            'host to lowercase: is lowercase' => [
                'url' => new Url('https://example.com'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host to lowercase: is uppercase' => [
                'url' => new Url('https://EXAMPLE.com'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host to lowercase: is mixed-case' => [
                'url' => new Url('https://eXampLE.com'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host dot removal, single dot, no path' => [
                'url' => new Url('https://example.com.'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host dot removal, double dot, no path' => [
                'url' => new Url('https://example.com..'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host dot removal, single dot, has path' => [
                'url' => new Url('https://example.com./foo'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com/foo'),
            ],
            'host dot removal, double dot, has path' => [
                'url' => new Url('https://example.com../foo'),
                'options' => [],
                'expectedUrl' => new Url('https://example.com/foo'),
            ],
            'host convertUnicodeToPunycode=false: is normal host' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => false,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host convertUnicodeToPunycode=false: is punycode host' => [
                'url' => new Url('https://artesan.xn--a-iga.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => false,
                ],
                'expectedUrl' => new Url('https://artesan.xn--a-iga.com'),
            ],
            'host convertUnicodeToPunycode=false: is unicode host' => [
                'url' => new Url('https://artesan.ía.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => false,
                ],
                'expectedUrl' => new Url('https://artesan.ía.com'),
            ],
            'host convertUnicodeToPunycode=true: is normal host' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'host convertUnicodeToPunycode=true: is punycode host' => [
                'url' => new Url('https://artesan.xn--a-iga.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => true,
                ],
                'expectedUrl' => new Url('https://artesan.xn--a-iga.com'),
            ],
            'host convertUnicodeToPunycode=true: is unicode host' => [
                'url' => new Url('https://artesan.ía.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => true,
                ],
                'expectedUrl' => new Url('https://artesan.xn--a-iga.com'),
            ],
        ];
    }

    public function removeFragmentDataProvider(): array
    {
        return [
            'removeFragment=false, no fragment' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => false,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeFragment=false, has fragment' => [
                'url' => new Url('http://example.com#foo'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => false,
                ],
                'expectedUrl' => new Url('http://example.com#foo'),
            ],
            'removeFragment=true, no fragment' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeFragment=true, has fragment' => [
                'url' => new Url('http://example.com#foo'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
        ];
    }

    public function removeWwwDataProvider(): array
    {
        return [
            'removeWww=false, no www' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => false,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeWww=false, has www' => [
                'url' => new Url('http://www.example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => false,
                ],
                'expectedUrl' => new Url('http://www.example.com'),
            ],
            'removeWww=true, no www' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeWww=true, has www' => [
                'url' => new Url('http://www.example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
        ];
    }
}
