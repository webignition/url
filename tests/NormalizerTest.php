<?php

namespace webignition\Url\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Url\Normalizer;
use webignition\Url\NormalizerOptions;
use webignition\Url\Url;

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
     * dataProvider schemeNormalizationDataProvider
     * @dataProvider forceHttpForceHttpsDataProvider
     * @dataProvider removeUserInfoDataProvider
     * @dataProvider hostNormalizationDataProvider
     * @dataProvider removeFragmentDataProvider
     * @dataProvider removeWwwDataProvider
     * dataProvider removeDefaultFilesPatternsDataProvider
     * dataProvider reduceMultipleTrailingSlashesDataProvider
     * @dataProvider removeDotPathSegmentsDataProvider
     * @dataProvider addTrailingSlashDataProvider
     * @dataProvider sortQueryParametersDataProvider
     * dataProvider defaultOptionsDataProvider
     * @dataProvider reduceDuplicatePathSlashesDataProvider
     *
     * @param UriInterface $url
     * @param int $flags
     * @param string $expectedUrl
     */
    public function testNormalize(UriInterface $url, int $flags, string $expectedUrl)
    {
        $normalizedUrl = $this->normalizer->normalize($url, $flags);

        $this->assertEquals((string) $expectedUrl, (string) $normalizedUrl);
    }

    public function schemeNormalizationDataProvider(): array
    {
        return [
            'applyDefaultSchemeIfNoScheme=false, no scheme' => [
                'url' => Url::create('example.com/foo/bar'),
                'flags' => [
                    NormalizerOptions::OPTION_APPLY_DEFAULT_SCHEME_IF_NO_SCHEME => false,
                ],
                'expectedUrl' => 'example.com/foo/bar',
            ],
            'applyDefaultSchemeIfNoScheme=false, no scheme, protocol-relative' => [
                'url' => Url::create('//example.com/foo/bar'),
                'flags' => [
                    NormalizerOptions::OPTION_APPLY_DEFAULT_SCHEME_IF_NO_SCHEME => false,
                ],
                'expectedUrl' => '//example.com/foo/bar',
            ],
            'applyDefaultSchemeIfNoScheme=true, no scheme (example.com is treated as path)' => [
                'url' => Url::create('example.com/foo/bar'),
                'flags' => [
                    NormalizerOptions::OPTION_APPLY_DEFAULT_SCHEME_IF_NO_SCHEME => true,
                ],
                'expectedUrl' => 'http:example.com/foo/bar',
            ],
            'applyDefaultSchemeIfNoScheme=true, no scheme, protocol-relative' => [
                'url' => Url::create('//example.com/foo/bar'),
                'flags' => [
                    NormalizerOptions::OPTION_APPLY_DEFAULT_SCHEME_IF_NO_SCHEME => true,
                ],
                'expectedUrl' => 'http://example.com/foo/bar',
            ],
        ];
    }

    public function forceHttpForceHttpsDataProvider(): array
    {
        return [
            'forceHttp: http url' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::FORCE_HTTP,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'forceHttp: https url' => [
                'url' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTP,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'forceHttps: http url' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::FORCE_HTTPS,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'forceHttps: https url' => [
                'url' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTPS,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'forceHttp and forceHttps: http url' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::FORCE_HTTP | Normalizer::FORCE_HTTPS,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'forceHttp and forceHttps: https url' => [
                'url' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTP | Normalizer::FORCE_HTTPS,
                'expectedUrl' => Url::create('https://example.com'),
            ],
        ];
    }

    public function removeUserInfoDataProvider(): array
    {
        return [
            'removeUserInfo=false: no user info' => [
                'url' => Url::create('https://example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'removeUserInfo=false: has user info' => [
                'url' => Url::create('https://user:password@example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('https://user:password@example.com'),
            ],
            'removeUserInfo=true: no user info' => [
                'url' => Url::create('https://example.com'),
                'flags' => Normalizer::REMOVE_USER_INFO,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'removeUserInfo=true: has user info' => [
                'url' => Url::create('https://user:password@example.com'),
                'flags' => Normalizer::REMOVE_USER_INFO,
                'expectedUrl' => Url::create('https://example.com'),
            ],
        ];
    }

    public function hostNormalizationDataProvider(): array
    {
        return [
//            'host dot removal, single dot, no path' => [
//                'url' => Url::create('https://example.com.'),
//                'flags' => [],
//                'expectedUrl' => Url::create('https://example.com'),
//            ],
//            'host dot removal, double dot, no path' => [
//                'url' => Url::create('https://example.com..'),
//                'flags' => [],
//                'expectedUrl' => Url::create('https://example.com'),
//            ],
//            'host dot removal, single dot, has path' => [
//                'url' => Url::create('https://example.com./foo'),
//                'flags' => [],
//                'expectedUrl' => Url::create('https://example.com/foo'),
//            ],
//            'host dot removal, double dot, has path' => [
//                'url' => Url::create('https://example.com../foo'),
//                'flags' => [],
//                'expectedUrl' => Url::create('https://example.com/foo'),
//            ],
            'host convertHostUnicodeToPunycode=false: is normal host' => [
                'url' => Url::create('https://example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'host convertHostUnicodeToPunycode=false: is punycode host' => [
                'url' => Url::create('https://artesan.xn--a-iga.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('https://artesan.xn--a-iga.com'),
            ],
            'host convertHostUnicodeToPunycode=false: is unicode host' => [
                'url' => Url::create('https://artesan.ía.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('https://artesan.ía.com'),
            ],
            'host convertHostUnicodeToPunycode=true: is normal host' => [
                'url' => Url::create('https://example.com'),
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'host convertHostUnicodeToPunycode=true: is punycode host' => [
                'url' => Url::create('https://artesan.xn--a-iga.com'),
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
                'expectedUrl' => Url::create('https://artesan.xn--a-iga.com'),
            ],
            'host convertHostUnicodeToPunycode=true: is unicode host' => [
                'url' => Url::create('https://artesan.ía.com'),
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
                'expectedUrl' => Url::create('https://artesan.xn--a-iga.com'),
            ],
        ];
    }

    public function removeFragmentDataProvider(): array
    {
        return [
            'removeFragment=false, no fragment' => [
                'url' => Url::create('http://example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeFragment=false, has fragment' => [
                'url' => Url::create('http://example.com#foo'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com#foo'),
            ],
            'removeFragment=true, no fragment' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_FRAGMENT,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeFragment=true, has fragment' => [
                'url' => Url::create('http://example.com#foo'),
                'flags' => Normalizer::REMOVE_FRAGMENT,
                'expectedUrl' => Url::create('http://example.com'),
            ],
        ];
    }

    public function removeWwwDataProvider(): array
    {
        return [
            'removeWww=false, no www' => [
                'url' => Url::create('http://example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeWww=false, has www' => [
                'url' => Url::create('http://www.example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://www.example.com'),
            ],
            'removeWww=true, no www' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_WWW,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeWww=true, has www' => [
                'url' => Url::create('http://www.example.com'),
                'flags' => Normalizer::REMOVE_WWW,
                'expectedUrl' => Url::create('http://example.com'),
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
                'url' => Url::create('http://example.com'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => [],
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=[], index.html filename' => [
                'url' => Url::create('http://example.com/index.html'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => [],
                ],
                'expectedUrl' => Url::create('http://example.com/index.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, empty path' => [
                'url' => Url::create('http://example.com'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, no filename' => [
                'url' => Url::create('http://example.com/'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeDefaultFilesPatterns=non-empty, foo-index.html filename' => [
                'url' => Url::create('http://example.com/foo-index.html'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com/foo-index.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, index-foo.html filename' => [
                'url' => Url::create('http://example.com/index-foo.html'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com/index-foo.html'),
            ],
            'removeDefaultFilesPatterns=non-empty, index.html filename' => [
                'url' => Url::create('http://example.com/index.html'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, index.js filename' => [
                'url' => Url::create('http://example.com/index.js'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, default.asp filename' => [
                'url' => Url::create('http://example.com/default.asp'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, Default.asp filename' => [
                'url' => Url::create('http://example.com/Default.asp'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDefaultFilesPatterns=non-empty, default.aspx filename' => [
                'url' => Url::create('http://example.com/default.aspx'),
                'flags' => [
                    NormalizerOptions::OPTION_REMOVE_DEFAULT_FILES_PATTERNS => $removeDefaultFilesPatterns,
                ],
                'expectedUrl' => Url::create('http://example.com'),
            ],
        ];
    }

    public function reduceMultipleTrailingSlashesDataProvider(): array
    {
        return [
            'removeMultipleTrailingSlashes: no trailing slash' => [
                'url' => Url::create('http://example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeMultipleTrailingSlashes: empty path, double trailing slash' => [
                'url' => Url::create('http://example.com//'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeMultipleTrailingSlashes: empty path, triple trailing slash' => [
                'url' => Url::create('http://example.com///'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeMultipleTrailingSlashes: double trailing slash' => [
                'url' => Url::create('http://example.com/one/two//'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/one/two/'),
            ],
            'removeMultipleTrailingSlashes: triple trailing slash' => [
                'url' => Url::create('http://example.com/one/two///'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/one/two/'),
            ],
            'removeMultipleTrailingSlashes: leading double slash, mid double slash, trailing double slash' => [
                'url' => Url::create('http://example.com//one//two//'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com//one//two/'),
            ],
            'removeMultipleTrailingSlashes: leading triple slash, mid triple slash, trailing triple slash' => [
                'url' => Url::create('http://example.com///one///two///'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com///one///two/'),
            ],
            'removeMultipleTrailingSlashes: double mid slash, no trailing slash' => [
                'url' => Url::create('http://example.com/one//two'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/one//two'),
            ],
        ];
    }

    public function removeDotPathSegmentsDataProvider(): array
    {
        return [
            'removeDotPathSegments=true, no path' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDotPathSegments=true, / path' => [
                'url' => Url::create('http://example.com/'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, single dot' => [
                'url' => Url::create('http://example.com/.'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, double dot' => [
                'url' => Url::create('http://example.com/..'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, rfc3986 5.2.4 example 1' => [
                'url' => Url::create('http://example.com/a/b/c/./../../g'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/a/g'),
            ],
            'removeDotPathSegments=true, rfc3986 5.2.4 example 2' => [
                'url' => Url::create('http://example.com/mid/content=5/../6'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/mid/6'),
            ],
            'removeDotPathSegments=true, many single dot' => [
                'url' => Url::create('http://example.com/././././././././././././././.'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDotPathSegments=true, many single dot, trailing slash' => [
                'url' => Url::create('http://example.com/./././././././././././././././'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'removeDotPathSegments=true, many double dot' => [
                'url' => Url::create('http://example.com/../../../../../..'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'removeDotPathSegments=true, many double dot, trailing slash' => [
                'url' => Url::create('http://example.com/../../../../../../'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
        ];
    }

    public function addTrailingSlashDataProvider(): array
    {
        return [
            'addTrailingSlash: no path, no trailing slash' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'addTrailingSlash: has path, no trailing slash' => [
                'url' => Url::create('http://example.com/foo'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
                'expectedUrl' => Url::create('http://example.com/foo/'),
            ],
            'addTrailingSlash: empty path, has trailing slash' => [
                'url' => Url::create('http://example.com/'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'addTrailingSlash: has path, has trailing slash' => [
                'url' => Url::create('http://example.com/foo/'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
                'expectedUrl' => Url::create('http://example.com/foo/'),
            ],
            'addTrailingSlash: has filename' => [
                'url' => Url::create('http://example.com/index.html'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
                'expectedUrl' => Url::create('http://example.com/index.html'),
            ],
        ];
    }

    public function sortQueryParametersDataProvider(): array
    {
        return [
            'sortQueryParameters=false; no query' => [
                'url' => Url::create('http://example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'sortQueryParameters=false; has query' => [
                'url' => Url::create('http://example.com?b=bear&a=apple&c=cow'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com?b=bear&a=apple&c=cow'),
            ],
            'sortQueryParameters=true; no query' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'sortQueryParameters=true; has query' => [
                'url' => Url::create('http://example.com?b=bear&a=apple&c=cow'),
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
                'expectedUrl' => Url::create('http://example.com?a=apple&b=bear&c=cow'),
            ],
            'sortQueryParameters=true; key without value' => [
                'url' => Url::create('http://example.com?key2&key1=value1'),
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
                'expectedUrl' => Url::create('http://example.com?key1=value1&key2'),
            ],
        ];
    }

    public function reduceDuplicatePathSlashesDataProvider(): array
    {
        return [
            'reduceDuplicatePathSlashes=false; no path' => [
                'url' => Url::create('http://example.com'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'reduceDuplicatePathSlashes=false; no duplicate slashes' => [
                'url' => Url::create('http://example.com/path'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com/path'),
            ],
            'reduceDuplicatePathSlashes=false; has duplicate slashes' => [
                'url' => Url::create('http://example.com//path//'),
                'flags' => 0,
                'expectedUrl' => Url::create('http://example.com//path//'),
            ],
            'reduceDuplicatePathSlashes=true; no path' => [
                'url' => Url::create('http://example.com'),
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'reduceDuplicatePathSlashes=true; no duplicate slashes' => [
                'url' => Url::create('http://example.com/path'),
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
                'expectedUrl' => Url::create('http://example.com/path'),
            ],
            'reduceDuplicatePathSlashes=true; has duplicate slashes' => [
                'url' => Url::create('http://example.com//path//'),
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
                'expectedUrl' => Url::create('http://example.com//path//'),
            ],
        ];
    }

    public function defaultOptionsDataProvider(): array
    {
        return [
            'default: default scheme is not set if missing' => [
                'url' => Url::create('//example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('//example.com'),
            ],
            'default: http is not forced' => [
                'url' => Url::create('https://example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'default: https is not forced' => [
                'url' => Url::create('http://example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'default: user info is not removed' => [
                'url' => Url::create('http://user:password@example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('http://user:password@example.com'),
            ],
            'default: unicode in domain is converted to punycode' => [
                'url' => Url::create('http://♥.example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('http://xn--g6h.example.com'),
            ],
            'default: fragment is not removed' => [
                'url' => Url::create('http://example.com#fragment'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com#fragment'),
            ],
            'default: www is not removed' => [
                'url' => Url::create('http://www.example.com'),
                'flags' => [],
                'expectedUrl' => Url::create('http://www.example.com'),
            ],
            'default: path dot segments are not removed' => [
                'url' => Url::create('http://example.com/././.'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/././.'),
            ],
            'default: path trailing slash is not added' => [
                'url' => Url::create('http://example.com/path'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com/path'),
            ],
            'default: query parameters are not sorted' => [
                'url' => Url::create('http://example.com?b=2&a=1'),
                'flags' => [],
                'expectedUrl' => Url::create('http://example.com?b=2&a=1'),
            ],
        ];
    }
}
