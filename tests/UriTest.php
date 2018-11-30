<?php

namespace webignition\Url\Tests;

use webignition\Url\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    const UNRESERVED_CHARACTERS = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';

    /**
     * @dataProvider createDataProvider
     *
     * @param string $uri
     * @param string $expectedScheme
     * @param string $expectedAuthority
     * @param string $expectedUserInfo
     * @param string $expectedHost
     * @param int|null $expectedPort
     * @param string $expectedPath
     * @param string $expectedQuery
     */
    public function testCreate(
        string $uri,
        string $expectedScheme,
        string $expectedAuthority,
        string $expectedUserInfo,
        string $expectedHost,
        ?int $expectedPort,
        string $expectedPath,
        string $expectedQuery
    ) {
        $uriObject = Uri::create($uri);

        $this->assertSame($expectedScheme, $uriObject->getScheme());
        $this->assertSame($expectedAuthority, $uriObject->getAuthority());
        $this->assertSame($expectedUserInfo, $uriObject->getUserInfo());
        $this->assertSame($expectedHost, $uriObject->getHost());
        $this->assertSame($expectedPort, $uriObject->getPort());
        $this->assertSame($expectedPath, $uriObject->getPath());
        $this->assertSame($expectedQuery, $uriObject->getQuery());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'uri' => '',
                'expectedScheme' => '',
                'expectedAuthority' => '',
                'expectedUserInfo' => '',
                'expectedHost' => '',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
            ],
            'scheme only' => [
                'uri' => 'file://',
                'expectedScheme' => 'file',
                'expectedAuthority' => '',
                'expectedUserInfo' => '',
                'expectedHost' => '',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
            ],
            'scheme, host' => [
                'uri' => 'http://example.com',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'example.com',
                'expectedUserInfo' => '',
                'expectedHost' => 'example.com',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
            ],
            'scheme, user, password, host' => [
                'uri' => 'http://user:password@example.com',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
            ],
            'scheme, user, password, host, port (default)' => [
                'uri' => 'http://user:password@example.com:80',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
            ],
            'scheme, user, password, host, port (non-default)' => [
                'uri' => 'http://user:password@example.com:8080',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com:8080',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => 8080,
                'expectedPath' => '',
                'expectedQuery' => '',
            ],
            'complete except path' => [
                'url' => 'http://user:password@example.com:8080?foo=bar#fragment',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com:8080',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => 8080,
                'expectedPath' => '',
                'expectedQuery' => 'foo=bar',
            ],
            'complete fully qualified' => [
                'url' => 'http://user:password@example.com:8080/path1/path2/filename.extension?foo=bar#fragment',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com:8080',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => 8080,
                'expectedPath' => '/path1/path2/filename.extension',
                'expectedQuery' => 'foo=bar',
            ],
        ];
    }

    /**
     * @dataProvider getSchemeDataProvider
     *
     * @param string $scheme
     * @param string $expectedScheme
     */
    public function testGetScheme(string $scheme, string $expectedScheme)
    {
        $uri = new Uri($scheme, '', '', null, '', '', '');

        $this->assertEquals($expectedScheme, $uri->getScheme());
    }

    public function getSchemeDataProvider(): array
    {
        return [
            'http lowercase' => [
                'scheme' => 'http',
                'expectedScheme' => 'http',
            ],
            'http uppercase' => [
                'scheme' => 'HTTP',
                'expectedScheme' => 'http',
            ],
            'https lowercase' => [
                'scheme' => 'https',
                'expectedScheme' => 'https',
            ],
            'https uppercase' => [
                'scheme' => 'HTTPS',
                'expectedScheme' => 'https',
            ],
        ];
    }

    /**
     * @dataProvider getAuthorityDataProvider
     *
     * @param Uri $uri
     * @param string $expectedAuthority
     */
    public function testGetAuthority(Uri $uri, string $expectedAuthority)
    {
        $this->assertSame($expectedAuthority, $uri->getAuthority());
    }

    public function getAuthorityDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uri' => Uri::create('http://example.com'),
                'expectedAuthority' => 'example.com',
            ],
            'scheme, host, user' => [
                'uri' => Uri::create('http://user@example.com'),
                'expectedAuthority' => 'user@example.com',
            ],
            'scheme, host, password' => [
                'uri' => Uri::create('http://:password@example.com'),
                'expectedAuthority' => ':password@example.com',
            ],
            'scheme, host, user, password' => [
                'uri' => Uri::create('http://user:password@example.com'),
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, default port (http)' => [
                'uri' => Uri::create('http://user:password@example.com:80'),
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, default port (https)' => [
                'uri' => Uri::create('https://user:password@example.com:443'),
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, non-default port (http)' => [
                'uri' => Uri::create('http://user:password@example.com:8080'),
                'expectedAuthority' => 'user:password@example.com:8080',
            ],
            'scheme, host, user, password, non-default port (https)' => [
                'uri' => Uri::create('https://user:password@example.com:4433'),
                'expectedAuthority' => 'user:password@example.com:4433',
            ],
        ];
    }

    /**
     * @dataProvider getUserInfoDataProvider
     *
     * @param Uri $uri
     * @param string $expectedUserInfo
     */
    public function testGetUserInfo(Uri $uri, string $expectedUserInfo)
    {
        $this->assertSame($expectedUserInfo, $uri->getUserInfo());
    }

    public function getUserInfoDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uri' => Uri::create('http://example.com'),
                'expectedUserInfo' => '',
            ],
            'scheme, host, user' => [
                'uri' => Uri::create('http://user@example.com'),
                'expectedUserInfo' => 'user',
            ],
            'scheme, host, password' => [
                'uri' => Uri::create('http://:password@example.com'),
                'expectedUserInfo' => ':password',
            ],
            'scheme, host, user, password' => [
                'uri' => Uri::create('http://user:password@example.com'),
                'expectedUserInfo' => 'user:password',
            ],
            'host' => [
                'uri' => Uri::create('example.com'),
                'expectedUserInfo' => '',
            ],
            'host, user (without scheme is indistinguishable from being the path)' => [
                'uri' => Uri::create('user@example.com'),
                'expectedUserInfo' => '',
            ],
            'host, password (without scheme is indistinguishable from being the path)' => [
                'uri' => Uri::create('password@example.com'),
                'expectedUserInfo' => '',
            ],
            'host, user, password (without scheme is indistinguishable from being the path)' => [
                'uri' => Uri::create('user:password@example.com'),
                'expectedUserInfo' => '',
            ],
        ];
    }

    /**
     * @dataProvider getHostDataProvider
     *
     * @param Uri $uri
     * @param string $expectedHost
     */
    public function testGetHost(Uri $uri, string $expectedHost)
    {
        $this->assertSame($expectedHost, $uri->getHost());
    }

    public function getHostDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uri' => Uri::create('http://example.com'),
                'expectedHost' => 'example.com',
            ],
            'scheme, host, port' => [
                'uri' => Uri::create('http://example.com:8080'),
                'expectedHost' => 'example.com',
            ],
            'scheme, host, userinfo' => [
                'uri' => Uri::create('http://user:password@example.com'),
                'expectedHost' => 'example.com',
            ],
            'scheme, host, path' => [
                'uri' => Uri::create('http://@example.com/path'),
                'expectedHost' => 'example.com',
            ],
            'scheme, host, path, fragment' => [
                'uri' => Uri::create('http://@example.com/path#fragment'),
                'expectedHost' => 'example.com',
            ],
        ];
    }

    /**
     * @dataProvider getPortDataProvider
     *
     * @param Uri $uri
     * @param int|null $expectedPort
     */
    public function testGetPort(Uri $uri, ?int $expectedPort)
    {
        $this->assertSame($expectedPort, $uri->getPort());
    }

    public function getPortDataProvider(): array
    {
        return [
            'no port' => [
                'uri' => Uri::create('http://example.com'),
                'expectedPort' => null,
            ],
            'http default port' => [
                'uri' => Uri::create('http://example.com:80'),
                'expectedPort' => null,
            ],
            'https default port' => [
                'uri' => Uri::create('https://example.com:443'),
                'expectedPort' => null,
            ],
            'http non-default port' => [
                'uri' => Uri::create('http://example.com:8080'),
                'expectedPort' => 8080,
            ],
            'https non-default port' => [
                'uri' => Uri::create('https://example.com:4433'),
                'expectedPort' => 4433,
            ],
        ];
    }

    /**
     * @dataProvider getPathDataProvider
     *
     * @param Uri $uri
     * @param string $expectedPath
     */
    public function testGetPath(Uri $uri, string $expectedPath)
    {
        $this->assertSame($expectedPath, $uri->getPath());
    }

    public function getPathDataProvider(): array
    {
        return [
            'relative path' => [
                'uri' => Uri::create('path'),
                'expectedPath' => 'path',
            ],
            'absolute path' => [
                'uri' => Uri::create('/path'),
                'expectedPath' => '/path',
            ],
            'absolute path, query' => [
                'uri' => Uri::create('/path?foo'),
                'expectedPath' => '/path',
            ],
            'absolute path, query, fragment' => [
                'uri' => Uri::create('/path?foo#bar'),
                'expectedPath' => '/path',
            ],
            'scheme, host, absolute path, query, fragment' => [
                'uri' => Uri::create('http://example.com/path?foo#bar'),
                'expectedPath' => '/path',
            ],
            'percent-encode spaces' => [
                'uri' => Uri::create('/pa th'),
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode multi-byte characters' => [
                'uri' => Uri::create('/€?€#€'),
                'expectedPath' => '/%E2%82%AC',
            ],
            'do not double encode' => [
                'uri' => Uri::create('/pa%20th'),
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode invalid percent encodings' => [
                'uri' => Uri::create('/pa%2-th'),
                'expectedPath' => '/pa%252-th',
            ],
            'do not encode path separators' => [
                'uri' => Uri::create('/pa/th//two'),
                'expectedPath' => '/pa/th//two',
            ],
            'do not encode unreserved characters' => [
                'uri' => Uri::create('/' . self::UNRESERVED_CHARACTERS),
                'expectedPath' => '/' . self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uri' => Uri::create('/p%61th'),
                'expectedPath' => '/p%61th',
            ],
        ];
    }

    /**
     * @dataProvider getQueryDataProvider
     *
     * @param Uri $uri
     * @param string $expectedQuery
     */
    public function testGetQuery(Uri $uri, string $expectedQuery)
    {
        $this->assertSame($expectedQuery, $uri->getQuery());
    }

    public function getQueryDataProvider(): array
    {
        return [
            'percent-encode spaces' => [
                'uri' => Uri::create('/?f o=b r'),
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'do not encode plus' => [
                'uri' => Uri::create('/?f+o=b+r'),
                'expectedQuery' => 'f+o=b+r',
            ],
            'percent-encode multi-byte characters' => [
                'uri' => Uri::create('/?€=€'),
                'expectedQuery' => '%E2%82%AC=%E2%82%AC',
            ],
            'do not double encode' => [
                'uri' => Uri::create('/?f%20o=b%20r'),
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'percent-encode invalid percent encodings' => [
                'uri' => Uri::create('/?f%2o=b%2r'),
                'expectedQuery' => 'f%252o=b%252r',
            ],
            'do not encode path separators' => [
                'uri' => Uri::create('?q=va/lue'),
                'expectedQuery' => 'q=va/lue',
            ],
            'do not encode unreserved characters' => [
                'uri' => Uri::create('/?' . self::UNRESERVED_CHARACTERS),
                'expectedQuery' => self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uri' => Uri::create('/?f%61r=b%61r'),
                'expectedQuery' => 'f%61r=b%61r',
            ],
        ];
    }
}
