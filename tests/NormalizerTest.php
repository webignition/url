<?php

namespace webignition\Url\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Url\Normalizer;
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
     * @dataProvider applyDefaultSchemeIfNoSchemeDataProvider
     * @dataProvider forceHttpForceHttpsDataProvider
     * @dataProvider removeUserInfoDataProvider
     * @dataProvider hostNormalizationDataProvider
     * @dataProvider removeFragmentDataProvider
     * @dataProvider removeWwwDataProvider
     * @dataProvider removePathFilesDataProvider
     * @dataProvider removeDotPathSegmentsDataProvider
     * @dataProvider addTrailingSlashDataProvider
     * @dataProvider sortQueryParametersDataProvider
     * @dataProvider reduceDuplicatePathSlashesDataProvider
     * @dataProvider defaultsDataProvider
     *
     * @param UriInterface $url
     * @param int $flags
     * @param array $options
     * @param string $expectedUrl
     */
    public function testNormalize(
        UriInterface $url,
        string $expectedUrl,
        int $flags = Normalizer::PRESERVING_NORMALIZATIONS,
        ?array $options = []
    ) {
        $normalizedUrl = $this->normalizer->normalize($url, $flags, $options);

        $this->assertEquals((string) $expectedUrl, (string) $normalizedUrl);
    }

    public function applyDefaultSchemeIfNoSchemeDataProvider(): array
    {
        return [
            'applyDefaultSchemeIfNoScheme: no scheme (example.com is treated as path)' => [
                'url' => Url::create('example.com/foo/bar'),
                'expectedUrl' => 'http:example.com/foo/bar',
                'flags' => Normalizer::APPLY_DEFAULT_SCHEME_IF_NO_SCHEME,
                'options' => [
                    Normalizer::OPTION_DEFAULT_SCHEME => 'http',
                ],
            ],
            'applyDefaultSchemeIfNoScheme: no scheme, protocol-relative' => [
                'url' => Url::create('//example.com/foo/bar'),
                'expectedUrl' => 'http://example.com/foo/bar',
                'flags' => Normalizer::APPLY_DEFAULT_SCHEME_IF_NO_SCHEME,
                'options' => [
                    Normalizer::OPTION_DEFAULT_SCHEME => 'http',
                ],
            ],
        ];
    }

    public function forceHttpForceHttpsDataProvider(): array
    {
        return [
            'forceHttp: http url' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::FORCE_HTTP,
            ],
            'forceHttp: https url' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::FORCE_HTTP,
            ],
            'forceHttps: http url' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTPS,
            ],
            'forceHttps: https url' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTPS,
            ],
            'forceHttp and forceHttps: http url' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTP | Normalizer::FORCE_HTTPS,
            ],
            'forceHttp and forceHttps: https url' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::FORCE_HTTP | Normalizer::FORCE_HTTPS,
            ],
        ];
    }

    public function removeUserInfoDataProvider(): array
    {
        return [
            'removeUserInfo: no user info' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::REMOVE_USER_INFO,
            ],
            'removeUserInfo: has user info' => [
                'url' => Url::create('https://user:password@example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::REMOVE_USER_INFO,
            ],
        ];
    }

    public function hostNormalizationDataProvider(): array
    {
        return [
            'convertHostUnicodeToPunycode: normal host' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
            ],
            'convertHostUnicodeToPunycode: punycode host' => [
                'url' => Url::create('https://artesan.xn--a-iga.com'),
                'expectedUrl' => Url::create('https://artesan.xn--a-iga.com'),
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
            ],
            'convertHostUnicodeToPunycode: unicode host' => [
                'url' => Url::create('https://artesan.ía.com'),
                'expectedUrl' => Url::create('https://artesan.xn--a-iga.com'),
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
            ],
        ];
    }

    public function removeFragmentDataProvider(): array
    {
        return [
            'removeFragment:, no fragment' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_FRAGMENT,
            ],
            'removeFragment:, has fragment' => [
                'url' => Url::create('http://example.com#foo'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_FRAGMENT,
            ],
        ];
    }

    public function removeWwwDataProvider(): array
    {
        return [
            'removeWww: no www' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),

                'flags' => Normalizer::REMOVE_WWW,
            ],
            'removeWww: has www' => [
                'url' => Url::create('http://www.example.com'),
                'expectedUrl' => Url::create('http://example.com'),

                'flags' => Normalizer::REMOVE_WWW,
            ],
        ];
    }

    public function removePathFilesDataProvider(): array
    {
        $patterns = [
            Normalizer::REMOVE_INDEX_FILE_PATTERN,
        ];

        $options = [
            Normalizer::OPTION_REMOVE_PATH_FILES_PATTERNS => $patterns,
        ];

        return [
            'removePathFilesPatterns: empty path' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => 0,
                'options' => $options,
            ],
            'removePathFilesPatterns: no filename' => [
                'url' => Url::create('http://example.com/'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => 0,
                'options' => $options,
            ],
            'removePathFilesPatterns: foo-index.html' => [
                'url' => Url::create('http://example.com/foo-index.html'),
                'expectedUrl' => Url::create('http://example.com/foo-index.html'),
                'flags' => 0,
                'options' => $options,
            ],
            'removePathFilesPatterns: index-foo.html' => [
                'url' => Url::create('http://example.com/index-foo.html'),
                'expectedUrl' => Url::create('http://example.com/index-foo.html'),
                'flags' => 0,
                'options' => $options,
            ],
            'removePathFilesPatterns: index.html' => [
                'url' => Url::create('http://example.com/index.html'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => 0,
                'options' => $options,
            ],
            'removePathFilesPatterns: index.js' => [
                'url' => Url::create('http://example.com/index.js'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => 0,
                'options' => $options,
            ],
        ];
    }

    public function removeDotPathSegmentsDataProvider(): array
    {
        return [
            'removeDotPathSegments: no path' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: / path' => [
                'url' => Url::create('http://example.com/'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: single dot' => [
                'url' => Url::create('http://example.com/.'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: double dot' => [
                'url' => Url::create('http://example.com/..'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: rfc3986 5.2.4 example 1' => [
                'url' => Url::create('http://example.com/a/b/c/./../../g'),
                'expectedUrl' => Url::create('http://example.com/a/g'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: rfc3986 5.2.4 example 2' => [
                'url' => Url::create('http://example.com/mid/content=5/../6'),
                'expectedUrl' => Url::create('http://example.com/mid/6'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many single dot' => [
                'url' => Url::create('http://example.com/././././././././././././././.'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many single dot, trailing slash' => [
                'url' => Url::create('http://example.com/./././././././././././././././'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many double dot' => [
                'url' => Url::create('http://example.com/../../../../../..'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many double dot, trailing slash' => [
                'url' => Url::create('http://example.com/../../../../../../'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
        ];
    }

    public function addTrailingSlashDataProvider(): array
    {
        return [
            'addTrailingSlash: no path, no trailing slash' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: has path, no trailing slash' => [
                'url' => Url::create('http://example.com/foo'),
                'expectedUrl' => Url::create('http://example.com/foo/'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: empty path, has trailing slash' => [
                'url' => Url::create('http://example.com/'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: has path, has trailing slash' => [
                'url' => Url::create('http://example.com/foo/'),
                'expectedUrl' => Url::create('http://example.com/foo/'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: has filename' => [
                'url' => Url::create('http://example.com/index.html'),
                'expectedUrl' => Url::create('http://example.com/index.html'),
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
        ];
    }

    public function sortQueryParametersDataProvider(): array
    {
        return [
            'sortQueryParameters: no query' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
            ],
            'sortQueryParameters: has query' => [
                'url' => Url::create('http://example.com?b=bear&a=apple&c=cow'),
                'expectedUrl' => Url::create('http://example.com?a=apple&b=bear&c=cow'),
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
            ],
            'sortQueryParameters: key without value' => [
                'url' => Url::create('http://example.com?key2&key1=value1'),
                'expectedUrl' => Url::create('http://example.com?key1=value1&key2'),
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
            ],
        ];
    }

    public function reduceDuplicatePathSlashesDataProvider(): array
    {
        return [
            'reduceDuplicatePathSlashes: no path' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
            ],
            'reduceDuplicatePathSlashes: no duplicate slashes' => [
                'url' => Url::create('http://example.com/path'),
                'expectedUrl' => Url::create('http://example.com/path'),
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
            ],
            'reduceDuplicatePathSlashes: has duplicate slashes' => [
                'url' => Url::create('http://example.com//path//'),
                'expectedUrl' => Url::create('http://example.com//path//'),
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
            ],
        ];
    }

    public function defaultsDataProvider(): array
    {
        return [
            'default: default scheme is not set if missing' => [
                'url' => Url::create('//example.com'),
                'expectedUrl' => Url::create('//example.com'),
            ],
            'default: http is not forced' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com'),
            ],
            'default: https is not forced' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'default: user info is not removed' => [
                'url' => Url::create('http://user:password@example.com'),
                'expectedUrl' => Url::create('http://user:password@example.com'),
            ],
            'default: unicode in domain is not converted to punycode' => [
                'url' => Url::create('http://♥.example.com'),
                'expectedUrl' => Url::create('http://♥.example.com'),
            ],
            'default: fragment is not removed' => [
                'url' => Url::create('http://example.com#fragment'),
                'expectedUrl' => Url::create('http://example.com#fragment'),
            ],
            'default: www is not removed' => [
                'url' => Url::create('http://www.example.com'),
                'expectedUrl' => Url::create('http://www.example.com'),
            ],
            'default: path dot segments are removed' => [
                'url' => Url::create('http://example.com/././.'),
                'expectedUrl' => Url::create('http://example.com'),
            ],
            'default: path trailing slash is not added' => [
                'url' => Url::create('http://example.com/path'),
                'expectedUrl' => Url::create('http://example.com/path'),
            ],
            'default: duplicate path slashes are not reduced' => [
                'url' => Url::create('http://example.com//path//'),
                'expectedUrl' => Url::create('http://example.com//path//'),
            ],
            'default: query parameters are not sorted' => [
                'url' => Url::create('http://example.com?b=2&a=1'),
                'expectedUrl' => Url::create('http://example.com?b=2&a=1'),
            ],
        ];
    }
}
