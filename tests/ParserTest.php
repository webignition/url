<?php

namespace webignition\Url\Tests;

use webignition\Url\Parser;
use webignition\Url\UrlInterface;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getPartsDataProvider
     *
     * @param string|null $url
     * @param array $expectedParts
     */
    public function testGetParts(?string $url, array $expectedParts)
    {
        $parser = new Parser($url);
        $parts = $parser->getParts();

        $this->assertEquals(array_keys($expectedParts), array_keys($parts));

        foreach ($expectedParts as $key => $value) {
            $this->assertEquals($expectedParts[$key], $parts[$key]);
        }
    }

    public function getPartsDataProvider(): array
    {
        return [
            'null' => [
                'url' => null,
                'expectedParts' => [
                    UrlInterface::PART_PATH => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'empty' => [
                'url' => '',
                'expectedParts' => [
                    UrlInterface::PART_PATH => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'complete fully qualified' => [
                'url' => 'http://user:pass@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'http',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_PORT => 8080,
                    UrlInterface::PART_USER => 'user',
                    UrlInterface::PART_PASS => 'pass',
                    UrlInterface::PART_PATH => '/path1/path2/filename.extension',
                    UrlInterface::PART_QUERY => 'foo=bar',
                    UrlInterface::PART_FRAGMENT => 'fragment',
                ],
            ],
            'complete protocol-relative' => [
                'url' => '//user:pass@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_PORT => 8080,
                    UrlInterface::PART_USER => 'user',
                    UrlInterface::PART_PASS => 'pass',
                    UrlInterface::PART_PATH => '/path1/path2/filename.extension',
                    UrlInterface::PART_QUERY => 'foo=bar',
                    UrlInterface::PART_FRAGMENT => 'fragment',
                ],
            ],
            'root relative' => [
                'url' => '/path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    UrlInterface::PART_PATH => '/path1/path2/filename.extension',
                    UrlInterface::PART_QUERY => 'foo=bar',
                    UrlInterface::PART_FRAGMENT => 'fragment',
                ],
            ],
            'relative' => [
                'url' => 'path1/path2/filename.extension?foo=bar#fragment',
                'expectedParts' => [
                    UrlInterface::PART_PATH => 'path1/path2/filename.extension',
                    UrlInterface::PART_QUERY => 'foo=bar',
                    UrlInterface::PART_FRAGMENT => 'fragment',
                ],
            ],
            'hash only' => [
                'url' => '#',
                'expectedParts' => [
                    UrlInterface::PART_FRAGMENT => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'path and hash only' => [
                'url' => '/index.html#',
                'expectedParts' => [
                    UrlInterface::PART_PATH => '/index.html',
                    UrlInterface::PART_FRAGMENT => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'hash and identifier only' => [
                'url' => '#fragment',
                'expectedParts' => [
                    UrlInterface::PART_FRAGMENT => 'fragment',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'scheme, no username, no password' => [
                'url' => 'https://@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'protocol-relative, no username, no password' => [
                'url' => '//@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'scheme, empty username, empty password' => [
                'url' => 'https://:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'protocol-relative, empty username, empty password' => [
                'url' => '//:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'scheme, empty username, has password' => [
                'url' => 'https://:password@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => 'password',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'protocol-relative, empty username, has password' => [
                'url' => '//:password@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => 'password',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'scheme, has username, empty password' => [
                'url' => 'https://username:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                    UrlInterface::PART_PASS => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'protocol-relative, has username, empty password' => [
                'url' => '//username:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                    UrlInterface::PART_PASS => '',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'scheme, has username, no password' => [
                'url' => 'https://username@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'protocol-relative, has username, no password' => [
                'url' => '//username@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
            'scheme-only' => [
                'url' => 'file://',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME  => 'file',
                    UrlInterface::PART_QUERY => '',
                ],
            ],
        ];
    }
}
