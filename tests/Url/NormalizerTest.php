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
     * @dataProvider removeKnownPortsDataProvider
     * @dataProvider removeDefaultFilesPatternsDataProvider
     * @dataProvider reduceMultipleTrailingSlashesDataProvider
     * @dataProvider removeDotPathSegmentsDataProvider
     * @dataProvider addTrailingSlashDataProvider
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

    public function removeKnownPortsDataProvider(): array
    {
        return [
            'removeKnownPorts=false, no port, http' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => false,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeKnownPorts=false, no port, https' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => false,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'removeKnownPorts=false, non-known port, http' => [
                'url' => new Url('http://example.com:8080'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => false,
                ],
                'expectedUrl' => new Url('http://example.com:8080'),
            ],
            'removeKnownPorts=false, non-known port, https' => [
                'url' => new Url('https://example.com:4433'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => false,
                ],
                'expectedUrl' => new Url('https://example.com:4433'),
            ],
            'removeKnownPorts=false, known port, http' => [
                'url' => new Url('http://example.com:80'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => false,
                ],
                'expectedUrl' => new Url('http://example.com:80'),
            ],
            'removeKnownPorts=false, known port, https' => [
                'url' => new Url('https://example.com:443'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => false,
                ],
                'expectedUrl' => new Url('https://example.com:443'),
            ],
            'removeKnownPorts=true, no port, http' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeKnownPorts=true, no port, https' => [
                'url' => new Url('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
            'removeKnownPorts=true, non-known port, http' => [
                'url' => new Url('http://example.com:8080'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => true,
                ],
                'expectedUrl' => new Url('http://example.com:8080'),
            ],
            'removeKnownPorts=true, non-known port, https' => [
                'url' => new Url('https://example.com:4433'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => true,
                ],
                'expectedUrl' => new Url('https://example.com:4433'),
            ],
            'removeKnownPorts=true, known port, http' => [
                'url' => new Url('http://example.com:80'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeKnownPorts=true, known port, https' => [
                'url' => new Url('https://example.com:443'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_KNOWN_PORTS => true,
                ],
                'expectedUrl' => new Url('https://example.com'),
            ],
        ];
    }

    public function removeDefaultFilesPatternsDataProvider(): array
    {
        $removeDefaultFilesPatterns = [
            NormalizerOptions::REMOVE_INDEX_FILE_PATTERN,
            NormalizerOptions::REMOVE_DEFAULT_FILE_PATTERN,
        ];

        return [
            'removeDefaultFilesPatterns=[], no filename' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => [],
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDefaultFilesPatterns=[], index.html filename' => [
                'url' => new Url('http://example.com/index.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => [],
                ],
                'expectedUrl' => new Url('http://example.com/index.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, foo-index.html filename' => [
                'url' => new Url('http://example.com/foo-index.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com/foo-index.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, index-foo.html filename' => [
                'url' => new Url('http://example.com/index-foo.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com/index-foo.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, index.html filename' => [
                'url' => new Url('http://example.com/index.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, index.js filename' => [
                'url' => new Url('http://example.com/index.js'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, default.asp filename' => [
                'url' => new Url('http://example.com/default.asp'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, Default.asp filename' => [
                'url' => new Url('http://example.com/Default.asp'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, default.aspx filename' => [
                'url' => new Url('http://example.com/default.aspx'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
        ];
    }

    public function reduceMultipleTrailingSlashesDataProvider(): array
    {
        return [
            'removeMultipleTrailingSlashes: no trailing slash' => [
                'url' => new Url('http://example.com'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeMultipleTrailingSlashes: empty path, double trailing slash' => [
                'url' => new Url('http://example.com//'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'removeMultipleTrailingSlashes: empty path, triple trailing slash' => [
                'url' => new Url('http://example.com///'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'removeMultipleTrailingSlashes: double trailing slash' => [
                'url' => new Url('http://example.com/one/two//'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/one/two/'),
            ],
            'removeMultipleTrailingSlashes: triple trailing slash' => [
                'url' => new Url('http://example.com/one/two///'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/one/two/'),
            ],
            'removeMultipleTrailingSlashes: leading double slash, mid double slash, trailing double slash' => [
                'url' => new Url('http://example.com//one//two//'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com//one//two/'),
            ],
            'removeMultipleTrailingSlashes: leading triple slash, mid triple slash, trailing triple slash' => [
                'url' => new Url('http://example.com///one///two///'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com///one///two/'),
            ],
            'removeMultipleTrailingSlashes: double mid slash, no trailing slash' => [
                'url' => new Url('http://example.com/one//two'),
                'options' => [],
                'expectedUrl' => new Url('http://example.com/one//two'),
            ],
        ];
    }

    public function removeDotPathSegmentsDataProvider(): array
    {
        return [
            'removeDotPathSegments=true, single dot' => [
                'url' => new Url('http://example.com/.'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'removeDotPathSegments=true, double dot' => [
                'url' => new Url('http://example.com/..'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'removeDotPathSegments=true, rfc3986 5.2.4 example 1' => [
                'url' => new Url('http://example.com/a/b/c/./../../g'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com/a/g'),
            ],
            'removeDotPathSegments=true, rfc3986 5.2.4 example 2' => [
                'url' => new Url('http://example.com/mid/content=5/../6'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com/mid/6'),
            ],
            'removeDotPathSegments=true, many single dot' => [
                'url' => new Url('http://example.com/././././././././././././././.'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDotPathSegments=true, many single dot, trailing slash' => [
                'url' => new Url('http://example.com/./././././././././././././././'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'removeDotPathSegments=true, many double dot' => [
                'url' => new Url('http://example.com/../../../../../..'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com'),
            ],
            'removeDotPathSegments=true, many double dot, trailing slash' => [
                'url' => new Url('http://example.com/../../../../../../'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => new Url('http://example.com/'),
            ],
        ];
    }

    public function addTrailingSlashDataProvider(): array
    {
        return [
            'addTrailingSlash: no path, no trailing slash' => [
                'url' => new Url('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'addTrailingSlash: has path, no trailing slash' => [
                'url' => new Url('http://example.com/foo'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => new Url('http://example.com/foo/'),
            ],
            'addTrailingSlash: empty path, has trailing slash' => [
                'url' => new Url('http://example.com/'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => new Url('http://example.com/'),
            ],
            'addTrailingSlash: has path, has trailing slash' => [
                'url' => new Url('http://example.com/foo/'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => new Url('http://example.com/foo/'),
            ],
        ];
    }
}
