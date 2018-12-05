<?php

namespace webignition\Url\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Url\Normalizer;
use webignition\Url\NormalizerOptions;
use webignition\Url\Uri;

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
     * @dataProvider removeDefaultFilesPatternsDataProvider
     * @dataProvider reduceMultipleTrailingSlashesDataProvider
     * @dataProvider removeDotPathSegmentsDataProvider
     * @dataProvider addTrailingSlashDataProvider
     * @dataProvider sortQueryParametersDataProvider
     * @dataProvider defaultOptionsDataProvider
     *
     * @param UriInterface $url
     * @param array $options
     * @param string $expectedUrl
     */
    public function testNormalize(UriInterface $url, array $options, string $expectedUrl)
    {
        $normalizedUrl = $this->normalizer->normalize($url, $options);

        $this->assertEquals((string) $expectedUrl, (string) $normalizedUrl);
    }

    public function schemeNormalizationDataProvider(): array
    {
        return [
            'setDefaultSchemeIfNoScheme=false, no scheme' => [
                'url' => Uri::create('example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => false,
                ],
                'expectedUrl' => 'example.com/foo/bar',
            ],
            'setDefaultSchemeIfNoScheme=false, no scheme, protocol-relative' => [
                'url' => Uri::create('//example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => false,
                ],
                'expectedUrl' => '//example.com/foo/bar',
            ],
            'setDefaultSchemeIfNoScheme=true, no scheme (example.com is treated as path)' => [
                'url' => Uri::create('example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => true,
                ],
                'expectedUrl' => 'http:example.com/foo/bar',
            ],
            'setDefaultSchemeIfNoScheme=true, no scheme, protocol-relative' => [
                'url' => Uri::create('//example.com/foo/bar'),
                'options' => [
                    NormalizerOptions::OPTION_SET_DEFAULT_SCHEME_IF_NO_SCHEME => true,
                ],
                'expectedUrl' => 'http://example.com/foo/bar',
            ],
        ];
    }

    public function forceHttpForceHttpsDataProvider(): array
    {
        return [
            'forceHttp: http url' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'forceHttp: https url' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'forceHttps: http url' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'forceHttps: https url' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'forceHttp and forceHttps: http url' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'forceHttp and forceHttps: https url' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_FORCE_HTTP => true,
                    NormalizerOptions::OPTION_FORCE_HTTPS => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
        ];
    }

    public function removeUserInfoDataProvider(): array
    {
        return [
            'removeUserInfo=false: no user info' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => false,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'removeUserInfo=false: has user info' => [
                'url' => Uri::create('https://user:password@example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => false,
                ],
                'expectedUrl' => Uri::create('https://user:password@example.com'),
            ],
            'removeUserInfo=true: no user info' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'removeUserInfo=true: has user info' => [
                'url' => Uri::create('https://user:password@example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_USER_INFO => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
        ];
    }

    public function hostNormalizationDataProvider(): array
    {
        return [
            'host dot removal, single dot, no path' => [
                'url' => Uri::create('https://example.com.'),
                'options' => [],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'host dot removal, double dot, no path' => [
                'url' => Uri::create('https://example.com..'),
                'options' => [],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'host dot removal, single dot, has path' => [
                'url' => Uri::create('https://example.com./foo'),
                'options' => [],
                'expectedUrl' => Uri::create('https://example.com/foo'),
            ],
            'host dot removal, double dot, has path' => [
                'url' => Uri::create('https://example.com../foo'),
                'options' => [],
                'expectedUrl' => Uri::create('https://example.com/foo'),
            ],
            'host convertUnicodeToPunycode=false: is normal host' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => false,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'host convertUnicodeToPunycode=false: is punycode host' => [
                'url' => Uri::create('https://artesan.xn--a-iga.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => false,
                ],
                'expectedUrl' => Uri::create('https://artesan.xn--a-iga.com'),
            ],
            'host convertUnicodeToPunycode=false: is unicode host' => [
                'url' => Uri::create('https://artesan.ía.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => false,
                ],
                'expectedUrl' => Uri::create('https://artesan.ía.com'),
            ],
            'host convertUnicodeToPunycode=true: is normal host' => [
                'url' => Uri::create('https://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => true,
                ],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'host convertUnicodeToPunycode=true: is punycode host' => [
                'url' => Uri::create('https://artesan.xn--a-iga.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => true,
                ],
                'expectedUrl' => Uri::create('https://artesan.xn--a-iga.com'),
            ],
            'host convertUnicodeToPunycode=true: is unicode host' => [
                'url' => Uri::create('https://artesan.ía.com'),
                'options' => [
                    NormalizerOptions::OPTION_CONVERT_UNICODE_TO_PUNYCODE => true,
                ],
                'expectedUrl' => Uri::create('https://artesan.xn--a-iga.com'),
            ],
        ];
    }

    public function removeFragmentDataProvider(): array
    {
        return [
            'removeFragment=false, no fragment' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => false,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeFragment=false, has fragment' => [
                'url' => Uri::create('http://example.com#foo'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => false,
                ],
                'expectedUrl' => Uri::create('http://example.com#foo'),
            ],
            'removeFragment=true, no fragment' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeFragment=true, has fragment' => [
                'url' => Uri::create('http://example.com#foo'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_FRAGMENT => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
        ];
    }

    public function removeWwwDataProvider(): array
    {
        return [
            'removeWww=false, no www' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => false,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeWww=false, has www' => [
                'url' => Uri::create('http://www.example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => false,
                ],
                'expectedUrl' => Uri::create('http://www.example.com'),
            ],
            'removeWww=true, no www' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeWww=true, has www' => [
                'url' => Uri::create('http://www.example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_WWW => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
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
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => [],
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=[], index.html filename' => [
                'url' => Uri::create('http://example.com/index.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => [],
                ],
                'expectedUrl' => Uri::create('http://example.com/index.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, empty path' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, no filename' => [
                'url' => Uri::create('http://example.com/'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeDefaultFilesPatterns=non-empty, foo-index.html filename' => [
                'url' => Uri::create('http://example.com/foo-index.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com/foo-index.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, index-foo.html filename' => [
                'url' => Uri::create('http://example.com/index-foo.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com/index-foo.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, index.html filename' => [
                'url' => Uri::create('http://example.com/index.html'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, index.js filename' => [
                'url' => Uri::create('http://example.com/index.js'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, default.asp filename' => [
                'url' => Uri::create('http://example.com/default.asp'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, Default.asp filename' => [
                'url' => Uri::create('http://example.com/Default.asp'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, default.aspx filename' => [
                'url' => Uri::create('http://example.com/default.aspx'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
        ];
    }

    public function reduceMultipleTrailingSlashesDataProvider(): array
    {
        return [
            'removeMultipleTrailingSlashes: no trailing slash' => [
                'url' => Uri::create('http://example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeMultipleTrailingSlashes: empty path, double trailing slash' => [
                'url' => Uri::create('http://example.com//'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeMultipleTrailingSlashes: empty path, triple trailing slash' => [
                'url' => Uri::create('http://example.com///'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeMultipleTrailingSlashes: double trailing slash' => [
                'url' => Uri::create('http://example.com/one/two//'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/one/two/'),
            ],
            'removeMultipleTrailingSlashes: triple trailing slash' => [
                'url' => Uri::create('http://example.com/one/two///'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/one/two/'),
            ],
            'removeMultipleTrailingSlashes: leading double slash, mid double slash, trailing double slash' => [
                'url' => Uri::create('http://example.com//one//two//'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com//one//two/'),
            ],
            'removeMultipleTrailingSlashes: leading triple slash, mid triple slash, trailing triple slash' => [
                'url' => Uri::create('http://example.com///one///two///'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com///one///two/'),
            ],
            'removeMultipleTrailingSlashes: double mid slash, no trailing slash' => [
                'url' => Uri::create('http://example.com/one//two'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/one//two'),
            ],
        ];
    }

    public function removeDotPathSegmentsDataProvider(): array
    {
        return [
            'removeDotPathSegments=true, no path' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDotPathSegments=true, / path' => [
                'url' => Uri::create('http://example.com/'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, single dot' => [
                'url' => Uri::create('http://example.com/.'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, double dot' => [
                'url' => Uri::create('http://example.com/..'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, rfc3986 5.2.4 example 1' => [
                'url' => Uri::create('http://example.com/a/b/c/./../../g'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/a/g'),
            ],
            'removeDotPathSegments=true, rfc3986 5.2.4 example 2' => [
                'url' => Uri::create('http://example.com/mid/content=5/../6'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/mid/6'),
            ],
            'removeDotPathSegments=true, many single dot' => [
                'url' => Uri::create('http://example.com/././././././././././././././.'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDotPathSegments=true, many single dot, trailing slash' => [
                'url' => Uri::create('http://example.com/./././././././././././././././'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, many double dot' => [
                'url' => Uri::create('http://example.com/../../../../../..'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'removeDotPathSegments=true, many double dot, trailing slash' => [
                'url' => Uri::create('http://example.com/../../../../../../'),
                'options' => [
                    NormalizerOptions::OPTION_REMOVE_PATH_DOT_SEGMENTS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
        ];
    }

    public function addTrailingSlashDataProvider(): array
    {
        return [
            'addTrailingSlash: no path, no trailing slash' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'addTrailingSlash: has path, no trailing slash' => [
                'url' => Uri::create('http://example.com/foo'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/foo/'),
            ],
            'addTrailingSlash: empty path, has trailing slash' => [
                'url' => Uri::create('http://example.com/'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/'),
            ],
            'addTrailingSlash: has path, has trailing slash' => [
                'url' => Uri::create('http://example.com/foo/'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/foo/'),
            ],
            'addTrailingSlash: has filename' => [
                'url' => Uri::create('http://example.com/index.html'),
                'options' => [
                    NormalizerOptions::OPTION_ADD_PATH_TRAILING_SLASH => true,
                ],
                'expectedUrl' => Uri::create('http://example.com/index.html'),
            ],
        ];
    }

    public function sortQueryParametersDataProvider(): array
    {
        return [
            'sortQueryParameters=false; no query' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_SORT_QUERY_PARAMETERS => false,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'sortQueryParameters=false; has query' => [
                'url' => Uri::create('http://example.com?b=bear&a=apple&c=cow'),
                'options' => [
                    NormalizerOptions::OPTION_SORT_QUERY_PARAMETERS => false,
                ],
                'expectedUrl' => Uri::create('http://example.com?b=bear&a=apple&c=cow'),
            ],
            'sortQueryParameters=true; no query' => [
                'url' => Uri::create('http://example.com'),
                'options' => [
                    NormalizerOptions::OPTION_SORT_QUERY_PARAMETERS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'sortQueryParameters=true; has query' => [
                'url' => Uri::create('http://example.com?b=bear&a=apple&c=cow'),
                'options' => [
                    NormalizerOptions::OPTION_SORT_QUERY_PARAMETERS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com?a=apple&b=bear&c=cow'),
            ],
            'sortQueryParameters=true; key without value' => [
                'url' => Uri::create('http://example.com?key2&key1=value1'),
                'options' => [
                    NormalizerOptions::OPTION_SORT_QUERY_PARAMETERS => true,
                ],
                'expectedUrl' => Uri::create('http://example.com?key1=value1&key2'),
            ],
        ];
    }

    public function defaultOptionsDataProvider(): array
    {
        return [
            'default: default scheme is not set if missing' => [
                'url' => Uri::create('//example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('//example.com'),
            ],
            'default: http is not forced' => [
                'url' => Uri::create('https://example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('https://example.com'),
            ],
            'default: https is not forced' => [
                'url' => Uri::create('http://example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com'),
            ],
            'default: user info is not removed' => [
                'url' => Uri::create('http://user:password@example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('http://user:password@example.com'),
            ],
            'default: unicode in domain is converted to punycode' => [
                'url' => Uri::create('http://♥.example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('http://xn--g6h.example.com'),
            ],
            'default: fragment is not removed' => [
                'url' => Uri::create('http://example.com#fragment'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com#fragment'),
            ],
            'default: www is not removed' => [
                'url' => Uri::create('http://www.example.com'),
                'options' => [],
                'expectedUrl' => Uri::create('http://www.example.com'),
            ],
            'default: path dot segments are not removed' => [
                'url' => Uri::create('http://example.com/././.'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/././.'),
            ],
            'default: path trailing slash is not added' => [
                'url' => Uri::create('http://example.com/path'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com/path'),
            ],
            'default: query parameters are not sorted' => [
                'url' => Uri::create('http://example.com?b=2&a=1'),
                'options' => [],
                'expectedUrl' => Uri::create('http://example.com?b=2&a=1'),
            ],
        ];
    }
}
