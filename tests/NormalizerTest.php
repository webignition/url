<?php

namespace webignition\Url\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Url\Normalizer;
use webignition\Url\Url;

class NormalizerTest extends \PHPUnit\Framework\TestCase
{
    const ALPHA_CHARACTERS = 'abcdefghijklmnopqrstuvwxyz';
    const NUMERIC_CHARACTERS = '0123456789';
    const UNRESERVED_NON_ALPHA_NUMERIC_CHARACTERS = '-._~';

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
     * @dataProvider removeUserInfoDataProvider
     * @dataProvider hostNormalizationDataProvider
     * @dataProvider removeFragmentDataProvider
     * @dataProvider removeWwwDataProvider
     * @dataProvider removePathFilesDataProvider
     * @dataProvider removeDotPathSegmentsDataProvider
     * @dataProvider addTrailingSlashDataProvider
     * @dataProvider sortQueryParametersDataProvider
     * @dataProvider reduceDuplicatePathSlashesDataProvider
     * @dataProvider decodeUnreservedCharactersDataProvider
     * @dataProvider removeDefaultPortDataProvider
     * @dataProvider capitalizePercentEncodingDataProvider
     * @dataProvider removeDefaultFileHostDataProvider
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

    public function decodeUnreservedCharactersDataProvider() : array
    {
        $characters = $this->createUnreservedCharactersString();
        $percentEncodedCharacters = $this->percentEncodeString($characters);

        return [
            'decodeUnreservedCharacters: ' => [
                'url' => Url::create('http://example.com/' . $percentEncodedCharacters),
                'expectedUrl' => Url::create('http://example.com/' . $characters),
                'flags' => Normalizer::DECODE_UNRESERVED_CHARACTERS,
            ],
        ];
    }

    public function removeDefaultPortDataProvider(): array
    {
        return [
            'removeDefaultPort: http url with port 80' => [
                'url' => $this->setUrlPort(Url::create('http://example.com:80'), 80),
                'expectedUrl' => Url::create('http://example.com'),
                'flags' => Normalizer::REMOVE_DEFAULT_PORT,
            ],
            'removeDefaultPort: https url with port 443' => [
                'url' => $this->setUrlPort(Url::create('https://example.com:443'), 443),
                'expectedUrl' => Url::create('https://example.com'),
                'flags' => Normalizer::REMOVE_DEFAULT_PORT,
            ],
        ];
    }

    public function capitalizePercentEncodingDataProvider(): array
    {
        $characters = $this->createUnreservedCharactersString();
        $percentEncodedCharacters = $this->percentEncodeString($characters);

        return [
            'capitalizePercentEncoding: lowercase' => [
                'url' => Url::create('http://example.com/' . strtolower($percentEncodedCharacters)),
                'expectedUrl' => Url::create('http://example.com/' . $percentEncodedCharacters),
                'flags' => Normalizer::CAPITALIZE_PERCENT_ENCODING,
            ],
            'capitalizePercentEncoding: uppercase' => [
                'url' => Url::create('http://example.com/' . $percentEncodedCharacters),
                'expectedUrl' => Url::create('http://example.com/' . $percentEncodedCharacters),
                'flags' => Normalizer::CAPITALIZE_PERCENT_ENCODING,
            ],
        ];
    }

    public function convertEmptyHttpPathDataProvider(): array
    {
        return [
            'convertEmptyHttpPath: http' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com/'),
                'flags' => Normalizer::CONVERT_EMPTY_HTTP_PATH,
            ],
            'convertEmptyHttpPath: https' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com/'),
                'flags' => Normalizer::CONVERT_EMPTY_HTTP_PATH,
            ],
        ];
    }

    public function removeDefaultFileHostDataProvider(): array
    {
        return [
            'removeDefaultFileHost: http' => [
                'url' => Url::create('file://localhost/path'),
                'expectedUrl' => Url::create('file:///path'),
                'flags' => Normalizer::REMOVE_DEFAULT_FILE_HOST,
            ],
        ];
    }

    public function defaultsDataProvider(): array
    {
        $unreservedCharacters = $this->createUnreservedCharactersString();
        $percentEncodedUnreservedCharacters = $this->percentEncodeString($unreservedCharacters);

        return [
            'default: default scheme is not set if missing' => [
                'url' => Url::create('//example.com/'),
                'expectedUrl' => Url::create('//example.com/'),
            ],
            'default: http is not forced' => [
                'url' => Url::create('https://example.com/'),
                'expectedUrl' => Url::create('https://example.com/'),
            ],
            'default: https is not forced' => [
                'url' => Url::create('http://example.com/'),
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'default: user info is not removed' => [
                'url' => Url::create('http://user:password@example.com/'),
                'expectedUrl' => Url::create('http://user:password@example.com/'),
            ],
            'default: unicode in domain is not converted to punycode' => [
                'url' => Url::create('http://♥.example.com/'),
                'expectedUrl' => Url::create('http://xn--g6h.example.com/'),
            ],
            'default: fragment is not removed' => [
                'url' => Url::create('http://example.com/#fragment'),
                'expectedUrl' => Url::create('http://example.com/#fragment'),
            ],
            'default: www is not removed' => [
                'url' => Url::create('http://www.example.com/'),
                'expectedUrl' => Url::create('http://www.example.com/'),
            ],
            'default: path dot segments are removed' => [
                'url' => Url::create('http://example.com/././.'),
                'expectedUrl' => Url::create('http://example.com/'),
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
                'url' => Url::create('http://example.com/?b=2&a=1'),
                'expectedUrl' => Url::create('http://example.com/?b=2&a=1'),
            ],
            'default: unreserved characters are decoded' => [
                'url' => Url::create('http://example.com/' . $percentEncodedUnreservedCharacters),
                'expectedUrl' => Url::create('http://example.com/' . $unreservedCharacters),
            ],
            'default: default port is removed' => [
                'url' => $this->setUrlPort(Url::create('http://example.com:80/'), 80),
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'default: percent encoding is capitalized' => [
                'url' => Url::create('http://example.com/?%2f'),
                'expectedUrl' => Url::create('http://example.com/?%2F'),
            ],
            'default: empty http path is converted' => [
                'url' => Url::create('http://example.com'),
                'expectedUrl' => Url::create('http://example.com/'),
            ],
            'default: empty https path is converted' => [
                'url' => Url::create('https://example.com'),
                'expectedUrl' => Url::create('https://example.com/'),
            ],
            'default: file localhost is removed' => [
                'url' => Url::create('file://localhost/path'),
                'expectedUrl' => Url::create('file:///path'),
            ],
        ];
    }

    private function createUnreservedCharactersString(): string
    {
        return strtoupper(self::ALPHA_CHARACTERS)
            . self::ALPHA_CHARACTERS
            . self::NUMERIC_CHARACTERS
            . self::UNRESERVED_NON_ALPHA_NUMERIC_CHARACTERS;
    }

    private function percentEncodeString(string $value): string
    {
        $charactersAsArray = str_split($value);

        array_walk($charactersAsArray, function (string &$character) {
            $character = '%' . strtoupper(dechex(ord($character)));
        });

        return implode('', $charactersAsArray);
    }

    private function setUrlPort(Url $url, int $port): Url
    {
        try {
            $reflector = new \ReflectionClass(Url::class);
            $property = $reflector->getProperty('port');
            $property->setAccessible(true);
            $property->setValue($url, $port);
        } catch (\ReflectionException $exception) {
        }

        return $url;
    }
}
