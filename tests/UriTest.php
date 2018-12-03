<?php

namespace webignition\Url\Tests;

use webignition\Url\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    const UNRESERVED_CHARACTERS = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';

    /**
     * @dataProvider createSuccessDataProvider
     *
     * @param string $uri
     * @param string $expectedScheme
     * @param string $expectedAuthority
     * @param string $expectedUserInfo
     * @param string $expectedHost
     * @param int|null $expectedPort
     * @param string $expectedPath
     * @param string $expectedQuery
     * @param string $expectedFragment
     */
    public function testCreateSuccess(
        string $uri,
        string $expectedScheme,
        string $expectedAuthority,
        string $expectedUserInfo,
        string $expectedHost,
        ?int $expectedPort,
        string $expectedPath,
        string $expectedQuery,
        string $expectedFragment
    ) {
        $uriObject = Uri::create($uri);

        $this->assertSame($expectedScheme, $uriObject->getScheme());
        $this->assertSame($expectedAuthority, $uriObject->getAuthority());
        $this->assertSame($expectedUserInfo, $uriObject->getUserInfo());
        $this->assertSame($expectedHost, $uriObject->getHost());
        $this->assertSame($expectedPort, $uriObject->getPort());
        $this->assertSame($expectedPath, $uriObject->getPath());
        $this->assertSame($expectedQuery, $uriObject->getQuery());
        $this->assertSame($expectedFragment, $uriObject->getFragment());
    }

    public function createSuccessDataProvider(): array
    {
        return [
            'empty' => [
                'uriString' => '',
                'expectedScheme' => '',
                'expectedAuthority' => '',
                'expectedUserInfo' => '',
                'expectedHost' => '',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'scheme only' => [
                'uriString' => 'file://',
                'expectedScheme' => 'file',
                'expectedAuthority' => '',
                'expectedUserInfo' => '',
                'expectedHost' => '',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'scheme, host' => [
                'uriString' => 'http://example.com',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'example.com',
                'expectedUserInfo' => '',
                'expectedHost' => 'example.com',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'scheme, user, password, host' => [
                'uriString' => 'http://user:password@example.com',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'scheme, user, password, host, port (default)' => [
                'uriString' => 'http://user:password@example.com:80',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => null,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'scheme, user, password, host, port (non-default)' => [
                'uriString' => 'http://user:password@example.com:8080',
                'expectedScheme' => 'http',
                'expectedAuthority' => 'user:password@example.com:8080',
                'expectedUserInfo' => 'user:password',
                'expectedHost' => 'example.com',
                'expectedPort' => 8080,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
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
                'expectedFragment' => 'fragment',
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
                'expectedFragment' => 'fragment',
            ],
        ];
    }

    /**
     * @dataProvider createWithInvalidPortDataProvider
     *
     * @param string $url
     */
    public function testCreateWithInvalidPort(string $url)
    {
        $this->expectException(\InvalidArgumentException::class);

        Uri::create($url);
    }

    public function createWithInvalidPortDataProvider(): array
    {
        return [
            'less than min' => [
                'url' => 'http://example.com:' . (Uri::MIN_PORT - 1),
            ],
            'greater than max' => [
                'url' => 'http://example.com:' . (Uri::MAX_PORT + 1),
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
     * @param string $uriString
     * @param string $expectedAuthority
     */
    public function testGetAuthority(string $uriString, string $expectedAuthority)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedAuthority, $uri->getAuthority());
    }

    public function getAuthorityDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uriString' => 'http://example.com',
                'expectedAuthority' => 'example.com',
            ],
            'scheme, host, user' => [
                'uriString' => 'http://user@example.com',
                'expectedAuthority' => 'user@example.com',
            ],
            'scheme, host, password' => [
                'uriString' => 'http://:password@example.com',
                'expectedAuthority' => ':password@example.com',
            ],
            'scheme, host, user, password' => [
                'uriString' => 'http://user:password@example.com',
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, default port (http' => [
                'uriString' => 'http://user:password@example.com:80',
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, default port (https' => [
                'uriString' => 'https://user:password@example.com:443',
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, non-default port (http' => [
                'uriString' => 'http://user:password@example.com:8080',
                'expectedAuthority' => 'user:password@example.com:8080',
            ],
            'scheme, host, user, password, non-default port (https' => [
                'uriString' => 'https://user:password@example.com:4433',
                'expectedAuthority' => 'user:password@example.com:4433',
            ],
        ];
    }

    /**
     * @dataProvider getUserInfoDataProvider
     *
     * @param string $uriString
     * @param string $expectedUserInfo
     */
    public function testGetUserInfo(string $uriString, string $expectedUserInfo)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedUserInfo, $uri->getUserInfo());
    }

    public function getUserInfoDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uriString' => 'http://example.com',
                'expectedUserInfo' => '',
            ],
            'scheme, host, user' => [
                'uriString' => 'http://user@example.com',
                'expectedUserInfo' => 'user',
            ],
            'scheme, host, password' => [
                'uriString' => 'http://:password@example.com',
                'expectedUserInfo' => ':password',
            ],
            'scheme, host, user, password' => [
                'uriString' => 'http://user:password@example.com',
                'expectedUserInfo' => 'user:password',
            ],
            'host' => [
                'uriString' => 'example.com',
                'expectedUserInfo' => '',
            ],
            'host, user (without scheme is indistinguishable from being the path' => [
                'uriString' => 'user@example.com',
                'expectedUserInfo' => '',
            ],
            'host, password (without scheme is indistinguishable from being the path' => [
                'uriString' => 'password@example.com',
                'expectedUserInfo' => '',
            ],
            'host, user, password (without scheme is indistinguishable from being the path' => [
                'uriString' => 'user:password@example.com',
                'expectedUserInfo' => '',
            ],
        ];
    }

    /**
     * @dataProvider getHostDataProvider
     *
     * @param string $uriString
     * @param string $expectedHost
     */
    public function testGetHost(string $uriString, string $expectedHost)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedHost, $uri->getHost());
    }

    public function getHostDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uriString' => 'http://example.com',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, port' => [
                'uriString' => 'http://example.com:8080',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, userinfo' => [
                'uriString' => 'http://user:password@example.com',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, path' => [
                'uriString' => 'http://@example.com/path',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, path, fragment' => [
                'uriString' => 'http://@example.com/path#fragment',
                'expectedHost' => 'example.com',
            ],
        ];
    }

    /**
     * @dataProvider getPortDataProvider
     *
     * @param string $uriString
     * @param int|null $expectedPort
     */
    public function testGetPort(string $uriString, ?int $expectedPort)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedPort, $uri->getPort());
    }

    public function getPortDataProvider(): array
    {
        return [
            'no port' => [
                'uriString' => 'http://example.com',
                'expectedPort' => null,
            ],
            'http default port' => [
                'uriString' => 'http://example.com:80',
                'expectedPort' => null,
            ],
            'https default port' => [
                'uriString' => 'https://example.com:443',
                'expectedPort' => null,
            ],
            'http non-default port' => [
                'uriString' => 'http://example.com:8080',
                'expectedPort' => 8080,
            ],
            'https non-default port' => [
                'uriString' => 'https://example.com:4433',
                'expectedPort' => 4433,
            ],
        ];
    }

    /**
     * @dataProvider getPathDataProvider
     *
     * @param string $uriString
     * @param string $expectedPath
     */
    public function testGetPath(string $uriString, string $expectedPath)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedPath, $uri->getPath());
    }

    public function getPathDataProvider(): array
    {
        return [
            'relative path' => [
                'uriString' => 'path',
                'expectedPath' => 'path',
            ],
            'absolute path' => [
                'uriString' => '/path',
                'expectedPath' => '/path',
            ],
            'absolute path, query' => [
                'uriString' => '/path?foo',
                'expectedPath' => '/path',
            ],
            'absolute path, query, fragment' => [
                'uriString' => '/path?foo#bar',
                'expectedPath' => '/path',
            ],
            'scheme, host, absolute path, query, fragment' => [
                'uriString' => 'http://example.com/path?foo#bar',
                'expectedPath' => '/path',
            ],
            'percent-encode spaces' => [
                'uriString' => '/pa th',
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode multi-byte characters' => [
                'uriString' => '/€?€#€',
                'expectedPath' => '/%E2%82%AC',
            ],
            'do not double encode' => [
                'uriString' => '/pa%20th',
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode invalid percent encodings' => [
                'uriString' => '/pa%2-th',
                'expectedPath' => '/pa%252-th',
            ],
            'do not encode path separators' => [
                'uriString' => '/pa/th//two',
                'expectedPath' => '/pa/th//two',
            ],
            'do not encode unreserved characters' => [
                'uriString' => '/' . self::UNRESERVED_CHARACTERS,
                'expectedPath' => '/' . self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uriString' => '/p%61th',
                'expectedPath' => '/p%61th',
            ],
        ];
    }

    /**
     * @dataProvider getQueryDataProvider
     *
     * @param string $uriString
     * @param string $expectedQuery
     */
    public function testGetQuery(string $uriString, string $expectedQuery)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedQuery, $uri->getQuery());
    }

    public function getQueryDataProvider(): array
    {
        return [
            'percent-encode spaces' => [
                'uriString' => '/?f o=b r',
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'do not encode plus' => [
                'uriString' => '/?f+o=b+r',
                'expectedQuery' => 'f+o=b+r',
            ],
            'percent-encode multi-byte characters' => [
                'uriString' => '/?€=€',
                'expectedQuery' => '%E2%82%AC=%E2%82%AC',
            ],
            'do not double encode' => [
                'uriString' => '/?f%20o=b%20r',
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'percent-encode invalid percent encodings' => [
                'uriString' => '/?f%2o=b%2r',
                'expectedQuery' => 'f%252o=b%252r',
            ],
            'do not encode path separators' => [
                'uriString' => '?q=va/lue',
                'expectedQuery' => 'q=va/lue',
            ],
            'do not encode unreserved characters' => [
                'uriString' => '/?' . self::UNRESERVED_CHARACTERS,
                'expectedQuery' => self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uriString' => '/?f%61r=b%61r',
                'expectedQuery' => 'f%61r=b%61r',
            ],
        ];
    }

    /**
     * @dataProvider getFragmentDataProvider
     *
     * @param string $uriString
     * @param string $expectedFragment
     */
    public function testGetFragment(string $uriString, string $expectedFragment)
    {
        $uri = Uri::create($uriString);

        $this->assertSame($expectedFragment, $uri->getFragment());
    }

    public function getFragmentDataProvider(): array
    {
        return [
            'percent-encode spaces' => [
                'uriString' => '/#f o',
                'expectedQuery' => 'f%20o',
            ],
            'do not encode plus' => [
                'uriString' => '/#f+o',
                'expectedQuery' => 'f+o',
            ],
            'percent-encode multi-byte characters' => [
                'uriString' => '/#€',
                'expectedQuery' => '%E2%82%AC',
            ],
            'do not double encode' => [
                'uriString' => '/#f%20o',
                'expectedQuery' => 'f%20o',
            ],
            'percent-encode invalid percent encodings' => [
                'uriString' => '/#f%2o',
                'expectedQuery' => 'f%252o',
            ],
            'do not encode path separators' => [
                'uriString' => '#f/o',
                'expectedQuery' => 'f/o',
            ],
            'do not encode unreserved characters' => [
                'uriString' => '/#' . self::UNRESERVED_CHARACTERS,
                'expectedQuery' => self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uriString' => '/#f%61r',
                'expectedQuery' => 'f%61r',
            ],
        ];
    }

    public function testWithScheme()
    {
        $originUri = Uri::create('http://example.com:443');
        $this->assertSame('http', $originUri->getScheme());
        $this->assertSame(443, $originUri->getPort());

        $nonUpdatedUri = $originUri->withScheme('http');
        $this->assertSame($originUri, $nonUpdatedUri);

        $updatedUri = $originUri->withScheme('HTTPS');
        $this->assertSame('https', $updatedUri->getScheme());
        $this->assertNull($updatedUri->getPort());
        $this->assertNotSame($originUri, $updatedUri);
    }

    public function testWithUserInfo()
    {
        $originUri = Uri::create('http://example.com');
        $this->assertSame('', $originUri->getUserInfo());

        $uriWithUserOnly = $originUri->withUserInfo('user');
        $this->assertNotSame($originUri, $uriWithUserOnly);
        $this->assertSame('user', $uriWithUserOnly->getUserInfo());

        $uriWithUserAndPassword = $uriWithUserOnly->withUserInfo('user-with-password', 'password');
        $this->assertNotSame($uriWithUserOnly, $uriWithUserAndPassword);
        $this->assertSame('user-with-password:password', $uriWithUserAndPassword->getUserInfo());
    }

    public function testWithHost()
    {
        $uriWithOnlyPath = Uri::create('/path');
        $this->assertSame('', $uriWithOnlyPath->getHost());

        $uriWithPathAndHost = $uriWithOnlyPath->withHost('example.com');
        $this->assertNotSame($uriWithOnlyPath, $uriWithPathAndHost);
        $this->assertSame('example.com', $uriWithPathAndHost->getHost());

        $uriWithChangedHost = $uriWithPathAndHost->withHost('foo.example.com');
        $this->assertSame('foo.example.com', $uriWithChangedHost->getHost());

        $uriWithRemovedHost = $uriWithPathAndHost->withHost('');
        $this->assertSame('', $uriWithRemovedHost->getHost());
    }

    /**
     * @dataProvider withPortInvalidPortDataProvider
     *
     * @param int $port
     */
    public function testWithPortInvalidPort(int $port)
    {
        $uri = Uri::create('http://example.co/');

        $this->expectException(\InvalidArgumentException::class);

        $uri->withPort($port);
    }

    public function withPortInvalidPortDataProvider(): array
    {
        return [
            'less than min' => [
                'port' => Uri::MIN_PORT - 1,
            ],
            'greater than max' => [
                'port' => Uri::MAX_PORT + 1,
            ],
        ];
    }

    public function testWithPort()
    {
        $httpUriWithoutPort = Uri::create('http://example.com');
        $this->assertNull($httpUriWithoutPort->getPort());

        $httpUriWithDefaultPortAdded = $httpUriWithoutPort->withPort(80);
        $this->assertNotSame($httpUriWithoutPort, $httpUriWithDefaultPortAdded);
        $this->assertNull($httpUriWithDefaultPortAdded->getPort());

        $httpUriWithNonDefaultPort = $httpUriWithDefaultPortAdded->withPort(8080);
        $this->assertSame(8080, $httpUriWithNonDefaultPort->getPort());
    }
}
