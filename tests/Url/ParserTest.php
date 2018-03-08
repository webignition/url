<?php

namespace webignition\Tests\Url;

use webignition\Url\Parser;
use webignition\Url\UrlInterface;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getPartsDataProvider
     *
     * @param string|null $url
     * @param array $expectedParts
     */
    public function testGetParts($url, array $expectedParts)
    {
        $parser = new Parser($url);
        $parts = $parser->getParts();

        $this->assertEquals(array_keys($expectedParts), array_keys($parts));

        foreach ($expectedParts as $key => $value) {
            $this->assertEquals($expectedParts[$key], $parts[$key]);
        }
    }

    /**
     * @return array
     */
    public function getPartsDataProvider()
    {
        return [
            'null' => [
                'url' => null,
                'expectedParts' => [
                    UrlInterface::PART_PATH => '',
                ],
            ],
            'empty' => [
                'url' => '',
                'expectedParts' => [
                    UrlInterface::PART_PATH => '',
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
                ],
            ],
            'path and hash only' => [
                'url' => '/index.html#',
                'expectedParts' => [
                    UrlInterface::PART_PATH => '/index.html',
                    UrlInterface::PART_FRAGMENT => '',
                ],
            ],
            'hash and identifier only' => [
                'url' => '#fragment',
                'expectedParts' => [
                    UrlInterface::PART_FRAGMENT => 'fragment',
                ],
            ],
            'scheme, no username, no password' => [
                'url' => 'https://@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                ],
            ],
            'protocol-relative, no username, no password' => [
                'url' => '//@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                ],
            ],
            'scheme, empty username, empty password' => [
                'url' => 'https://:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => '',
                ],
            ],
            'protocol-relative, empty username, empty password' => [
                'url' => '//:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => '',
                ],
            ],
            'scheme, empty username, has password' => [
                'url' => 'https://:password@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => 'password',
                ],
            ],
            'protocol-relative, empty username, has password' => [
                'url' => '//:password@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => '',
                    UrlInterface::PART_PASS => 'password',
                ],
            ],
            'scheme, has username, empty password' => [
                'url' => 'https://username:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                    UrlInterface::PART_PASS => '',
                ],
            ],
            'protocol-relative, has username, empty password' => [
                'url' => '//username:@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                    UrlInterface::PART_PASS => '',
                ],
            ],
            'scheme, has username, no password' => [
                'url' => 'https://username@example.com',
                'expectedParts' => [
                    UrlInterface::PART_SCHEME => 'https',
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                ],
            ],
            'protocol-relative, has username, no password' => [
                'url' => '//username@example.com',
                'expectedParts' => [
                    UrlInterface::PART_HOST => 'example.com',
                    UrlInterface::PART_USER => 'username',
                ],
            ],
        ];
    }
}
