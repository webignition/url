<?php

namespace webignition\Url\Tests;

use webignition\Url\Normalizer;
use webignition\Url\Url;

class NormalizerTest extends \PHPUnit\Framework\TestCase
{
    const ALPHA_CHARACTERS = 'abcdefghijklmnopqrstuvwxyz';
    const NUMERIC_CHARACTERS = '0123456789';
    const UNRESERVED_NON_ALPHA_NUMERIC_CHARACTERS = '-._~';

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
     * @dataProvider removeQueryParametersDataProvider
     * @dataProvider defaultsDataProvider
     *
     * @param string $url
     * @param int $flags
     * @param array $options
     * @param string $expectedUrl
     */
    public function testNormalize(
        string $url,
        string $expectedUrl,
        int $flags = Normalizer::PRESERVING_NORMALIZATIONS,
        ?array $options = []
    ) {
        $normalizedUrl = Normalizer::normalize(new Url($url), $flags, $options);

        $this->assertEquals((string) $expectedUrl, (string) $normalizedUrl);
    }

    public function removeUserInfoDataProvider(): array
    {
        return [
            'removeUserInfo: no user info' => [
                'url' => 'https://example.com',
                'expectedUrl' => 'https://example.com',
                'flags' => Normalizer::REMOVE_USER_INFO,
            ],
            'removeUserInfo: has user info' => [
                'url' => 'https://user:password@example.com',
                'expectedUrl' => 'https://example.com',
                'flags' => Normalizer::REMOVE_USER_INFO,
            ],
        ];
    }

    public function hostNormalizationDataProvider(): array
    {
        return [
            'convertHostUnicodeToPunycode: normal host' => [
                'url' => 'https://example.com',
                'expectedUrl' => 'https://example.com',
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
            ],
            'convertHostUnicodeToPunycode: punycode host' => [
                'url' => 'https://artesan.xn--a-iga.com',
                'expectedUrl' => 'https://artesan.xn--a-iga.com',
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
            ],
            'convertHostUnicodeToPunycode: unicode host' => [
                'url' => 'https://artesan.ía.com',
                'expectedUrl' => 'https://artesan.xn--a-iga.com',
                'flags' => Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE,
            ],
        ];
    }

    public function removeFragmentDataProvider(): array
    {
        return [
            'removeFragment:, no fragment' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_FRAGMENT,
            ],
            'removeFragment:, has fragment' => [
                'url' => 'http://example.com#foo',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_FRAGMENT,
            ],
        ];
    }

    public function removeWwwDataProvider(): array
    {
        return [
            'removeWww: no www' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_WWW,
            ],
            'removeWww: has www' => [
                'url' => 'http://www.example.com',
                'expectedUrl' => 'http://example.com',
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
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::NONE,
                'options' => $options,
            ],
            'removePathFilesPatterns: no filename' => [
                'url' => 'http://example.com/',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::NONE,
                'options' => $options,
            ],
            'removePathFilesPatterns: foo-index.html' => [
                'url' => 'http://example.com/foo-index.html',
                'expectedUrl' => 'http://example.com/foo-index.html',
                'flags' => Normalizer::NONE,
                'options' => $options,
            ],
            'removePathFilesPatterns: index-foo.html' => [
                'url' => 'http://example.com/index-foo.html',
                'expectedUrl' => 'http://example.com/index-foo.html',
                'flags' => Normalizer::NONE,
                'options' => $options,
            ],
            'removePathFilesPatterns: index.html' => [
                'url' => 'http://example.com/index.html',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::NONE,
                'options' => $options,
            ],
            'removePathFilesPatterns: index.js' => [
                'url' => 'http://example.com/index.js',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::NONE,
                'options' => $options,
            ],
        ];
    }

    public function removeDotPathSegmentsDataProvider(): array
    {
        return [
            'removeDotPathSegments: no path' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: / path' => [
                'url' => 'http://example.com/',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: single dot' => [
                'url' => 'http://example.com/.',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: double dot' => [
                'url' => 'http://example.com/..',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: rfc3986 5.2.4 example 1' => [
                'url' => 'http://example.com/a/b/c/./../../g',
                'expectedUrl' => 'http://example.com/a/g',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: rfc3986 5.2.4 example 2' => [
                'url' => 'http://example.com/mid/content=5/../6',
                'expectedUrl' => 'http://example.com/mid/6',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many single dot' => [
                'url' => 'http://example.com/././././././././././././././.',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many single dot, trailing slash' => [
                'url' => 'http://example.com/./././././././././././././././',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many double dot' => [
                'url' => 'http://example.com/../../../../../..',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
            'removeDotPathSegments: many double dot, trailing slash' => [
                'url' => 'http://example.com/../../../../../../',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::REMOVE_PATH_DOT_SEGMENTS,
            ],
        ];
    }

    public function addTrailingSlashDataProvider(): array
    {
        return [
            'addTrailingSlash: no path, no trailing slash' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: has path, no trailing slash' => [
                'url' => 'http://example.com/foo',
                'expectedUrl' => 'http://example.com/foo/',
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: empty path, has trailing slash' => [
                'url' => 'http://example.com/',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: has path, has trailing slash' => [
                'url' => 'http://example.com/foo/',
                'expectedUrl' => 'http://example.com/foo/',
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
            'addTrailingSlash: has filename' => [
                'url' => 'http://example.com/index.html',
                'expectedUrl' => 'http://example.com/index.html',
                'flags' => Normalizer::ADD_PATH_TRAILING_SLASH,
            ],
        ];
    }

    public function sortQueryParametersDataProvider(): array
    {
        return [
            'sortQueryParameters: no query' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
            ],
            'sortQueryParameters: has query' => [
                'url' => 'http://example.com?b=bear&a=apple&c=cow',
                'expectedUrl' => 'http://example.com?a=apple&b=bear&c=cow',
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
            ],
            'sortQueryParameters: key without value' => [
                'url' => 'http://example.com?key2&key1=value1',
                'expectedUrl' => 'http://example.com?key1=value1&key2',
                'flags' => Normalizer::SORT_QUERY_PARAMETERS,
            ],
        ];
    }

    public function reduceDuplicatePathSlashesDataProvider(): array
    {
        return [
            'reduceDuplicatePathSlashes: no path' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
            ],
            'reduceDuplicatePathSlashes: no duplicate slashes' => [
                'url' => 'http://example.com/path',
                'expectedUrl' => 'http://example.com/path',
                'flags' => Normalizer::REDUCE_DUPLICATE_PATH_SLASHES,
            ],
            'reduceDuplicatePathSlashes: has duplicate slashes' => [
                'url' => 'http://example.com//path//',
                'expectedUrl' => 'http://example.com//path//',
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
                'url' => 'http://example.com/' . $percentEncodedCharacters,
                'expectedUrl' => 'http://example.com/' . $characters,
                'flags' => Normalizer::DECODE_UNRESERVED_CHARACTERS,
            ],
        ];
    }

    public function removeDefaultPortDataProvider(): array
    {
        return [
            'removeDefaultPort: http url with port 80' => [
                'url' => $this->setUrlPort('http://example.com:80', 80),
                'expectedUrl' => 'http://example.com',
                'flags' => Normalizer::REMOVE_DEFAULT_PORT,
            ],
            'removeDefaultPort: https url with port 443' => [
                'url' => $this->setUrlPort('https://example.com:443', 443),
                'expectedUrl' => 'https://example.com',
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
                'url' => 'http://example.com/' . strtolower($percentEncodedCharacters),
                'expectedUrl' => 'http://example.com/' . $percentEncodedCharacters,
                'flags' => Normalizer::CAPITALIZE_PERCENT_ENCODING,
            ],
            'capitalizePercentEncoding: uppercase' => [
                'url' => 'http://example.com/' . $percentEncodedCharacters,
                'expectedUrl' => 'http://example.com/' . $percentEncodedCharacters,
                'flags' => Normalizer::CAPITALIZE_PERCENT_ENCODING,
            ],
        ];
    }

    public function convertEmptyHttpPathDataProvider(): array
    {
        return [
            'convertEmptyHttpPath: http' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::CONVERT_EMPTY_HTTP_PATH,
            ],
            'convertEmptyHttpPath: https' => [
                'url' => 'https://example.com',
                'expectedUrl' => 'https://example.com/',
                'flags' => Normalizer::CONVERT_EMPTY_HTTP_PATH,
            ],
        ];
    }

    public function removeDefaultFileHostDataProvider(): array
    {
        return [
            'removeDefaultFileHost: http' => [
                'url' => 'file://localhost/path',
                'expectedUrl' => 'file:///path',
                'flags' => Normalizer::REMOVE_DEFAULT_FILE_HOST,
            ],
        ];
    }

    public function removeQueryParametersDataProvider(): array
    {
        $url = 'http://example.com/?foo=bar&fizz=buzz&foobar&fizzbuzz';

        return [
            'removeQueryParameters: patterns not an array' => [
                'url' => $url,
                'expectedUrl' => $url,
                'flags' => Normalizer::NONE,
                'options' => [
                    Normalizer::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS => false,
                ],
            ],
            'removeQueryParameters: empty patterns' => [
                'url' => $url,
                'expectedUrl' => $url,
                'flags' => Normalizer::NONE,
                'options' => [
                    Normalizer::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS => [],
                ],
            ],
            'removeQueryParameters: non-empty patterns (1)' => [
                'url' => $url,
                'expectedUrl' => 'http://example.com/',
                'flags' => Normalizer::NONE,
                'options' => [
                    Normalizer::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS => [
                        '/^f[a-z]+$/'
                    ],
                ],
            ],
            'removeQueryParameters: non-empty patterns (2)' => [
                'url' => $url,
                'expectedUrl' => 'http://example.com/?fizz=buzz&fizzbuzz',
                'flags' => Normalizer::NONE,
                'options' => [
                    Normalizer::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS => [
                        '/^foo/'
                    ],
                ],
            ],
            'removeQueryParameters: non-empty patterns (3)' => [
                'url' => $url,
                'expectedUrl' => 'http://example.com/?foobar&fizzbuzz',
                'flags' => Normalizer::NONE,
                'options' => [
                    Normalizer::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS => [
                        '/^(foo|fizz)$/'
                    ],
                ],
            ],
        ];
    }

    public function defaultsDataProvider(): array
    {
        $unreservedCharacters = $this->createUnreservedCharactersString();
        $percentEncodedUnreservedCharacters = $this->percentEncodeString($unreservedCharacters);

        return [
            'default: default scheme is not set if missing' => [
                'url' => '//example.com/',
                'expectedUrl' => '//example.com/',
            ],
            'default: http is not forced' => [
                'url' => 'https://example.com/',
                'expectedUrl' => 'https://example.com/',
            ],
            'default: https is not forced' => [
                'url' => 'http://example.com/',
                'expectedUrl' => 'http://example.com/',
            ],
            'default: user info is not removed' => [
                'url' => 'http://user:password@example.com/',
                'expectedUrl' => 'http://user:password@example.com/',
            ],
            'default: unicode in domain is not converted to punycode' => [
                'url' => 'http://♥.example.com/',
                'expectedUrl' => 'http://xn--g6h.example.com/',
            ],
            'default: fragment is not removed' => [
                'url' => 'http://example.com/#fragment',
                'expectedUrl' => 'http://example.com/#fragment',
            ],
            'default: www is not removed' => [
                'url' => 'http://www.example.com/',
                'expectedUrl' => 'http://www.example.com/',
            ],
            'default: path dot segments are removed' => [
                'url' => 'http://example.com/././.',
                'expectedUrl' => 'http://example.com/',
            ],
            'default: path trailing slash is not added' => [
                'url' => 'http://example.com/path',
                'expectedUrl' => 'http://example.com/path',
            ],
            'default: duplicate path slashes are not reduced' => [
                'url' => 'http://example.com//path//',
                'expectedUrl' => 'http://example.com//path//',
            ],
            'default: query parameters are not sorted' => [
                'url' => 'http://example.com/?b=2&a=1',
                'expectedUrl' => 'http://example.com/?b=2&a=1',
            ],
            'default: unreserved characters are decoded' => [
                'url' => 'http://example.com/' . $percentEncodedUnreservedCharacters,
                'expectedUrl' => 'http://example.com/' . $unreservedCharacters,
            ],
            'default: default port is removed' => [
                'url' => $this->setUrlPort('http://example.com:80/', 80),
                'expectedUrl' => 'http://example.com/',
            ],
            'default: percent encoding is capitalized' => [
                'url' => 'http://example.com/?%2f',
                'expectedUrl' => 'http://example.com/?%2F',
            ],
            'default: empty http path is converted' => [
                'url' => 'http://example.com',
                'expectedUrl' => 'http://example.com/',
            ],
            'default: empty https path is converted' => [
                'url' => 'https://example.com',
                'expectedUrl' => 'https://example.com/',
            ],
            'default: file localhost is removed' => [
                'url' => 'file://localhost/path',
                'expectedUrl' => 'file:///path',
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

    private function setUrlPort(string $url, int $port): string
    {
        $urlObject = new Url($url);

        try {
            $reflector = new \ReflectionClass(Url::class);
            $property = $reflector->getProperty('port');
            $property->setAccessible(true);
            $property->setValue($urlObject, $port);
        } catch (\ReflectionException $exception) {
        }

        return (string) $urlObject;
    }
}
