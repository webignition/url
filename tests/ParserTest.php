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
     * @param array $expectedComponents
     */
    public function testParse(string $url, array $expectedComponents)
    {
        $components = Parser::parse($url);

        $this->assertEquals($expectedComponents, $components);
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'url' => '',
                'expectedComponents' => [],
            ],
            'complete fully qualified' => [
                'url' => 'http://user:pass@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => 8080,
                    Parser::COMPONENT_USER => 'user',
                    Parser::COMPONENT_PASS => 'pass',
                    Parser::COMPONENT_PATH => '/path1/path2/filename.extension',
                    Parser::COMPONENT_QUERY => 'foo=bar',
                    Parser::COMPONENT_FRAGMENT => 'fragment',
                ],
            ],
            'complete protocol-relative' => [
                'url' => '//user:pass@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => 8080,
                    Parser::COMPONENT_USER => 'user',
                    Parser::COMPONENT_PASS => 'pass',
                    Parser::COMPONENT_PATH => '/path1/path2/filename.extension',
                    Parser::COMPONENT_QUERY => 'foo=bar',
                    Parser::COMPONENT_FRAGMENT => 'fragment',
                ],
            ],
            'root relative' => [
                'url' => '/path1/path2/filename.extension?foo=bar#fragment',
                'expectedComponents' => [
                    Parser::COMPONENT_PATH => '/path1/path2/filename.extension',
                    Parser::COMPONENT_QUERY => 'foo=bar',
                    Parser::COMPONENT_FRAGMENT => 'fragment',
                ],
            ],
            'relative' => [
                'url' => 'path1/path2/filename.extension?foo=bar#fragment',
                'expectedComponents' => [
                    Parser::COMPONENT_PATH => 'path1/path2/filename.extension',
                    Parser::COMPONENT_QUERY => 'foo=bar',
                    Parser::COMPONENT_FRAGMENT => 'fragment',
                ],
            ],
            'hash only' => [
                'url' => '#',
                'expectedComponents' => [
                    Parser::COMPONENT_FRAGMENT => '',
                ],
            ],
            'path and hash only' => [
                'url' => '/index.html#',
                'expectedComponents' => [
                    Parser::COMPONENT_PATH => '/index.html',
                    Parser::COMPONENT_FRAGMENT => '',
                ],
            ],
            'hash and identifier only' => [
                'url' => '#fragment',
                'expectedComponents' => [
                    Parser::COMPONENT_FRAGMENT => 'fragment',
                ],
            ],
            'scheme, no username, no password' => [
                'url' => 'https://@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'https',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => '',
                ],
            ],
            'protocol-relative, no username, no password' => [
                'url' => '//@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => '',
                ],
            ],
            'scheme, empty username, empty password' => [
                'url' => 'https://:@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'https',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => '',
                    Parser::COMPONENT_PASS => '',
                ],
            ],
            'protocol-relative, empty username, empty password' => [
                'url' => '//:@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => '',
                    Parser::COMPONENT_PASS => '',
                ],
            ],
            'scheme, empty username, has password' => [
                'url' => 'https://:password@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'https',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => '',
                    Parser::COMPONENT_PASS => 'password',
                ],
            ],
            'protocol-relative, empty username, has password' => [
                'url' => '//:password@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => '',
                    Parser::COMPONENT_PASS => 'password',
                ],
            ],
            'scheme, has username, empty password' => [
                'url' => 'https://username:@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'https',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => 'username',
                    Parser::COMPONENT_PASS => '',
                ],
            ],
            'protocol-relative, has username, empty password' => [
                'url' => '//username:@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => 'username',
                    Parser::COMPONENT_PASS => '',
                ],
            ],
            'scheme, has username, no password' => [
                'url' => 'https://username@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'https',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => 'username',
                ],
            ],
            'protocol-relative, has username, no password' => [
                'url' => '//username@example.com',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_USER => 'username',
                ],
            ],
            'scheme-only (file_' => [
                'url' => 'file://',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME  => 'file',
                ],
            ],
            'scheme-only (http)' => [
                'url' => 'http://',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME  => 'http',
                ],
            ],
        ];
    }

    public function normalizeWhitespaceDataProvider(): array
    {
        return [
            'trailing tab is removed' => [
                'url' => "http://example.com\t",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                ],
            ],
            'trailing newline is removed' => [
                'url' => "http://example.com\n",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                ],
            ],
            'trailing line return' => [
                'url' => "http://example.com\r",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                ],
            ],
            'leading tab is removed' => [
                'url' => "\thttp://example.com",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                ],
            ],
            'leading newline is removed' => [
                'url' => "\nhttp://example.com",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                ],
            ],
            'leading line return' => [
                'url' => "\nhttp://example.com",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                ],
            ],
            'tab in path is removed' => [
                'url' => "http://example.com/foo\t/bar",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PATH => '/foo/bar',
                ],
            ],
            'newline in path is removed' => [
                'url' => "http://example.com/foo\n/bar",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PATH => '/foo/bar',
                ],
            ],
            'line return in path is removed' => [
                'url' => "http://example.com/foo\r/bar",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PATH => '/foo/bar',
                ],
            ],
            'many tabs, newlines and line returns' => [
                'url' => "\n\thttp://example.com\r\n/\rpage/\t",
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PATH => '/page/',
                ],
            ],
        ];
    }

    public function invalidPortDataProvider(): array
    {
        return [
            'invalid port (not an integer), no path' => [
                'url' => 'http://example.com:foo',
                'expectedComponents' => [],
            ],
            'invalid port (too small), no path' => [
                'url' => 'http://example.com:0',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                ],
            ],
            'invalid port (too small), protocol-relative, no path' => [
                'url' => '//example.com:0',
                'expectedComponents' => [
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                ],
            ],
            'invalid port (too small), path only' => [
                'url' => ':0/path',
                'expectedComponents' => [
                    Parser::COMPONENT_PORT => '0',
                    Parser::COMPONENT_PATH => '/path',
                ],
            ],
            'invalid port (too large), no path' => [
                'url' => 'http://example.com:65536',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '65536',
                ],
            ],
            'invalid port (too small), with path' => [
                'url' => 'http://example.com:0/path',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                    Parser::COMPONENT_PATH => '/path',
                ],
            ],
            'invalid port (too small), with path containing port-like pattern' => [
                'url' => 'http://example.com:0/path:0/path',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                    Parser::COMPONENT_PATH => '/path:0/path',
                ],
            ],
            'invalid port (too small), with query containing port-like pattern' => [
                'url' => 'http://example.com:0?:0',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                    Parser::COMPONENT_QUERY => ':0',
                ],
            ],
            'invalid port (too small), with fragment containing port-like pattern' => [
                'url' => 'http://example.com:0#:0',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                    Parser::COMPONENT_FRAGMENT => ':0',
                ],
            ],
            'invalid port (too small), with fragment containing port-like pattern and query-like pattern' => [
                'url' => 'http://example.com:0#:0?:0',
                'expectedComponents' => [
                    Parser::COMPONENT_SCHEME => 'http',
                    Parser::COMPONENT_HOST => 'example.com',
                    Parser::COMPONENT_PORT => '0',
                    Parser::COMPONENT_FRAGMENT => ':0?:0',
                ],
            ],
        ];
    }
}
