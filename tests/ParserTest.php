<?php

namespace webignition\Url\Tests;

use webignition\Url\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider parseDataProvider
     * @dataProvider normalizeWhitespaceDataProvider
     * @dataProvider invalidPortDataProvider
     *
     * @param string $url
     * @param array $expectedParts
     */
    public function testParse(string $url, array $expectedParts)
    {
        $parts = Parser::parse($url);

        $this->assertEquals($expectedParts, $parts);
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'url' => '',
                'expectedParts' => [],
            ],
            'complete fully qualified' => [
                'url' => 'http://user:pass@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => 8080,
                    Parser::PART_USER => 'user',
                    Parser::PART_PASS => 'pass',
                    Parser::PART_PATH => '/path1/path2/filename.extension',
                    Parser::PART_QUERY => 'foo=bar',
                    Parser::PART_FRAGMENT => 'fragment',
                ],
            ],
            'complete protocol-relative' => [
                'url' => '//user:pass@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => 8080,
                    Parser::PART_USER => 'user',
                    Parser::PART_PASS => 'pass',
                    Parser::PART_PATH => '/path1/path2/filename.extension',
                    Parser::PART_QUERY => 'foo=bar',
                    Parser::PART_FRAGMENT => 'fragment',
                ],
            ],
            'root relative' => [
                'url' => '/path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    Parser::PART_PATH => '/path1/path2/filename.extension',
                    Parser::PART_QUERY => 'foo=bar',
                    Parser::PART_FRAGMENT => 'fragment',
                ],
            ],
            'relative' => [
                'url' => 'path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    Parser::PART_PATH => 'path1/path2/filename.extension',
                    Parser::PART_QUERY => 'foo=bar',
                    Parser::PART_FRAGMENT => 'fragment',
                ],
            ],
            'hash only' => [
                'url' => '#',
                'expectedParts' => [
                    Parser::PART_FRAGMENT => '',
                ],
            ],
            'path and hash only' => [
                'url' => '/index.html#',
                'expectedParts' => [
                    Parser::PART_PATH => '/index.html',
                    Parser::PART_FRAGMENT => '',
                ],
            ],
            'hash and identifier only' => [
                'url' => '#fragment',
                'expectedParts' => [
                    Parser::PART_FRAGMENT => 'fragment',
                ],
            ],
            'scheme, no username, no password' => [
                'url' => 'https://@example.com',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'https',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => '',
                ],
            ],
            'protocol-relative, no username, no password' => [
                'url' => '//@example.com',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => '',
                ],
            ],
            'scheme, empty username, empty password' => [
                'url' => 'https://:@example.com',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'https',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => '',
                    Parser::PART_PASS => '',
                ],
            ],
            'protocol-relative, empty username, empty password' => [
                'url' => '//:@example.com',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => '',
                    Parser::PART_PASS => '',
                ],
            ],
            'scheme, empty username, has password' => [
                'url' => 'https://:password@example.com',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'https',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => '',
                    Parser::PART_PASS => 'password',
                ],
            ],
            'protocol-relative, empty username, has password' => [
                'url' => '//:password@example.com',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => '',
                    Parser::PART_PASS => 'password',
                ],
            ],
            'scheme, has username, empty password' => [
                'url' => 'https://username:@example.com',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'https',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => 'username',
                    Parser::PART_PASS => '',
                ],
            ],
            'protocol-relative, has username, empty password' => [
                'url' => '//username:@example.com',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => 'username',
                    Parser::PART_PASS => '',
                ],
            ],
            'scheme, has username, no password' => [
                'url' => 'https://username@example.com',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'https',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => 'username',
                ],
            ],
            'protocol-relative, has username, no password' => [
                'url' => '//username@example.com',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_USER => 'username',
                ],
            ],
            'scheme-only (file_' => [
                'url' => 'file://',
                'expectedParts' => [
                    Parser::PART_SCHEME  => 'file',
                ],
            ],
            'scheme-only (http)' => [
                'url' => 'http://',
                'expectedParts' => [
                    Parser::PART_SCHEME  => 'http',
                ],
            ],
        ];
    }

    public function normalizeWhitespaceDataProvider(): array
    {
        return [
            'trailing tab is removed' => [
                'url' => "http://example.com\t",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                ],
            ],
            'trailing newline is removed' => [
                'url' => "http://example.com\n",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                ],
            ],
            'trailing line return' => [
                'url' => "http://example.com\r",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                ],
            ],
            'leading tab is removed' => [
                'url' => "\thttp://example.com",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                ],
            ],
            'leading newline is removed' => [
                'url' => "\nhttp://example.com",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                ],
            ],
            'leading line return' => [
                'url' => "\nhttp://example.com",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                ],
            ],
            'tab in path is removed' => [
                'url' => "http://example.com/foo\t/bar",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PATH => '/foo/bar',
                ],
            ],
            'newline in path is removed' => [
                'url' => "http://example.com/foo\n/bar",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PATH => '/foo/bar',
                ],
            ],
            'line return in path is removed' => [
                'url' => "http://example.com/foo\r/bar",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PATH => '/foo/bar',
                ],
            ],
            'many tabs, newlines and line returns' => [
                'url' => "\n\thttp://example.com\r\n/\rpage/\t",
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PATH => '/page/',
                ],
            ],
        ];
    }

    public function invalidPortDataProvider(): array
    {
        return [
            'invalid port (not an integer), no path' => [
                'url' => 'http://example.com:foo',
                'expectedParts' => [],
            ],
            'invalid port (too small), no path' => [
                'url' => 'http://example.com:0',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                ],
            ],
            'invalid port (too small), protocol-relative, no path' => [
                'url' => '//example.com:0',
                'expectedParts' => [
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                ],
            ],
            'invalid port (too small), path only' => [
                'url' => ':0/path',
                'expectedParts' => [
                    Parser::PART_PORT => '0',
                    Parser::PART_PATH => '/path',
                ],
            ],
            'invalid port (too large), no path' => [
                'url' => 'http://example.com:65536',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '65536',
                ],
            ],
            'invalid port (too small), with path' => [
                'url' => 'http://example.com:0/path',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                    Parser::PART_PATH => '/path',
                ],
            ],
            'invalid port (too small), with path containing port-like pattern' => [
                'url' => 'http://example.com:0/path:0/path',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                    Parser::PART_PATH => '/path:0/path',
                ],
            ],
            'invalid port (too small), with query containing port-like pattern' => [
                'url' => 'http://example.com:0?:0',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                    Parser::PART_QUERY => ':0',
                ],
            ],
            'invalid port (too small), with fragment containing port-like pattern' => [
                'url' => 'http://example.com:0#:0',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                    Parser::PART_FRAGMENT => ':0',
                ],
            ],
            'invalid port (too small), with fragment containing port-like pattern and query-like pattern' => [
                'url' => 'http://example.com:0#:0?:0',
                'expectedParts' => [
                    Parser::PART_SCHEME => 'http',
                    Parser::PART_HOST => 'example.com',
                    Parser::PART_PORT => '0',
                    Parser::PART_FRAGMENT => ':0?:0',
                ],
            ],
        ];
    }
}
