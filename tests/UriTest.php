<?php

namespace webignition\Url\Tests;

use webignition\Url\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $uri
     * @param string $expectedScheme
     * @param string $expectedAuthority
     * @param string $expectedUserInfo
     */
    public function testCreate(
        string $uri,
        string $expectedScheme,
        string $expectedAuthority,
        string $expectedUserInfo
    ) {
        $uriObject = Uri::create($uri);

        $this->assertSame($expectedScheme, $uriObject->getScheme());
        $this->assertSame($expectedAuthority, $uriObject->getAuthority());
        $this->assertSame($expectedUserInfo, $uriObject->getUserInfo());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'uri' => '',
                'expectedScheme' => '',
                'expectedAuthority' => '',
                'expectedUserInfo' => '',
            ],
            'scheme only' => [
                'uri' => 'http://',
                'expectedScheme' => 'http',
                'expectedAuthority' => '',
                'expectedUserInfo' => '',
            ],
            'scheme, host, user, password' => [
                'uri' => 'http://user:password@example.com',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com',
                'expectedUserInfo' => 'user:password',
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
}
