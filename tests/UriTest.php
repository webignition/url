<?php

namespace webignition\Url\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Url\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    const UNRESERVED_CHARACTERS = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';

    const URI_FIELD_AUTHORITY = 'authority';
    const URI_FIELD_FRAGMENT = 'fragment';
    const URI_FIELD_HOST = 'host';
    const URI_FIELD_PATH = 'path';
    const URI_FIELD_PORT = 'port';
    const URI_FIELD_QUERY = 'query';
    const URI_FIELD_SCHEME = 'scheme';
    const URI_FIELD_USERINFO = 'userinfo';

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
     * @param string $uri
     * @param string $expectedAuthority
     */
    public function testGetAuthority(string $uri, string $expectedAuthority)
    {
        $this->assertSame($expectedAuthority, (Uri::create($uri))->getAuthority());
    }

    public function getAuthorityDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uri' => 'http://example.com',
                'expectedAuthority' => 'example.com',
            ],
            'scheme, host, user' => [
                'uri' => 'http://user@example.com',
                'expectedAuthority' => 'user@example.com',
            ],
            'scheme, host, password' => [
                'uri' => 'http://:password@example.com',
                'expectedAuthority' => ':password@example.com',
            ],
            'scheme, host, user, password' => [
                'uri' => 'http://user:password@example.com',
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, default port (http' => [
                'uri' => 'http://user:password@example.com:80',
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, default port (https' => [
                'uri' => 'https://user:password@example.com:443',
                'expectedAuthority' => 'user:password@example.com',
            ],
            'scheme, host, user, password, non-default port (http' => [
                'uri' => 'http://user:password@example.com:8080',
                'expectedAuthority' => 'user:password@example.com:8080',
            ],
            'scheme, host, user, password, non-default port (https' => [
                'uri' => 'https://user:password@example.com:4433',
                'expectedAuthority' => 'user:password@example.com:4433',
            ],
        ];
    }

    /**
     * @dataProvider getUserInfoDataProvider
     *
     * @param string $uri
     * @param string $expectedUserInfo
     */
    public function testGetUserInfo(string $uri, string $expectedUserInfo)
    {
        $this->assertSame($expectedUserInfo, (Uri::create($uri))->getUserInfo());
    }

    public function getUserInfoDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uri' => 'http://example.com',
                'expectedUserInfo' => '',
            ],
            'scheme, host, user' => [
                'uri' => 'http://user@example.com',
                'expectedUserInfo' => 'user',
            ],
            'scheme, host, password' => [
                'uri' => 'http://:password@example.com',
                'expectedUserInfo' => ':password',
            ],
            'scheme, host, user, password' => [
                'uri' => 'http://user:password@example.com',
                'expectedUserInfo' => 'user:password',
            ],
            'host' => [
                'uri' => 'example.com',
                'expectedUserInfo' => '',
            ],
            'host, user (without scheme is indistinguishable from being the path' => [
                'uri' => 'user@example.com',
                'expectedUserInfo' => '',
            ],
            'host, password (without scheme is indistinguishable from being the path' => [
                'uri' => 'password@example.com',
                'expectedUserInfo' => '',
            ],
            'host, user, password (without scheme is indistinguishable from being the path' => [
                'uri' => 'user:password@example.com',
                'expectedUserInfo' => '',
            ],
        ];
    }

    /**
     * @dataProvider getHostDataProvider
     *
     * @param string $uri
     * @param string $expectedHost
     */
    public function testGetHost(string $uri, string $expectedHost)
    {
        $this->assertSame($expectedHost, (Uri::create($uri))->getHost());
    }

    public function getHostDataProvider(): array
    {
        return [
            'scheme, host' => [
                'uri' => 'http://example.com',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, port' => [
                'uri' => 'http://example.com:8080',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, userinfo' => [
                'uri' => 'http://user:password@example.com',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, path' => [
                'uri' => 'http://@example.com/path',
                'expectedHost' => 'example.com',
            ],
            'scheme, host, path, fragment' => [
                'uri' => 'http://@example.com/path#fragment',
                'expectedHost' => 'example.com',
            ],
        ];
    }

    /**
     * @dataProvider getPortDataProvider
     *
     * @param string $uri
     * @param int|null $expectedPort
     */
    public function testGetPort(string $uri, ?int $expectedPort)
    {
        $this->assertSame($expectedPort, (Uri::create($uri))->getPort());
    }

    public function getPortDataProvider(): array
    {
        return [
            'no port' => [
                'uri' => 'http://example.com',
                'expectedPort' => null,
            ],
            'http default port' => [
                'uri' => 'http://example.com:80',
                'expectedPort' => null,
            ],
            'https default port' => [
                'uri' => 'https://example.com:443',
                'expectedPort' => null,
            ],
            'http non-default port' => [
                'uri' => 'http://example.com:8080',
                'expectedPort' => 8080,
            ],
            'https non-default port' => [
                'uri' => 'https://example.com:4433',
                'expectedPort' => 4433,
            ],
        ];
    }

    /**
     * @dataProvider getPathDataProvider
     *
     * @param string $uri
     * @param string $expectedPath
     */
    public function testGetPath(string $uri, string $expectedPath)
    {
        $this->assertSame($expectedPath, (Uri::create($uri))->getPath());
    }

    public function getPathDataProvider(): array
    {
        return [
            'relative path' => [
                'uri' => 'path',
                'expectedPath' => 'path',
            ],
            'absolute path' => [
                'uri' => '/path',
                'expectedPath' => '/path',
            ],
            'absolute path, query' => [
                'uri' => '/path?foo',
                'expectedPath' => '/path',
            ],
            'absolute path, query, fragment' => [
                'uri' => '/path?foo#bar',
                'expectedPath' => '/path',
            ],
            'scheme, host, absolute path, query, fragment' => [
                'uri' => 'http://example.com/path?foo#bar',
                'expectedPath' => '/path',
            ],
            'percent-encode spaces' => [
                'uri' => '/pa th',
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode multi-byte characters' => [
                'uri' => '/€?€#€',
                'expectedPath' => '/%E2%82%AC',
            ],
            'do not double encode' => [
                'uri' => '/pa%20th',
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode invalid percent encodings' => [
                'uri' => '/pa%2-th',
                'expectedPath' => '/pa%252-th',
            ],
            'do not encode path separators' => [
                'uri' => '/pa/th//two',
                'expectedPath' => '/pa/th//two',
            ],
            'do not encode unreserved characters' => [
                'uri' => '/' . self::UNRESERVED_CHARACTERS,
                'expectedPath' => '/' . self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uri' => '/p%61th',
                'expectedPath' => '/p%61th',
            ],
        ];
    }

    /**
     * @dataProvider getQueryDataProvider
     *
     * @param string $uri
     * @param string $expectedQuery
     */
    public function testGetQuery(string $uri, string $expectedQuery)
    {
        $this->assertSame($expectedQuery, (Uri::create($uri))->getQuery());
    }

    public function getQueryDataProvider(): array
    {
        return [
            'percent-encode spaces' => [
                'uri' => '/?f o=b r',
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'do not encode plus' => [
                'uri' => '/?f+o=b+r',
                'expectedQuery' => 'f+o=b+r',
            ],
            'percent-encode multi-byte characters' => [
                'uri' => '/?€=€',
                'expectedQuery' => '%E2%82%AC=%E2%82%AC',
            ],
            'do not double encode' => [
                'uri' => '/?f%20o=b%20r',
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'percent-encode invalid percent encodings' => [
                'uri' => '/?f%2o=b%2r',
                'expectedQuery' => 'f%252o=b%252r',
            ],
            'do not encode path separators' => [
                'uri' => '?q=va/lue',
                'expectedQuery' => 'q=va/lue',
            ],
            'do not encode unreserved characters' => [
                'uri' => '/?' . self::UNRESERVED_CHARACTERS,
                'expectedQuery' => self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uri' => '/?f%61r=b%61r',
                'expectedQuery' => 'f%61r=b%61r',
            ],
        ];
    }

    /**
     * @dataProvider getFragmentDataProvider
     *
     * @param string $uri
     * @param string $expectedFragment
     */
    public function testGetFragment(string $uri, string $expectedFragment)
    {
        $this->assertSame($expectedFragment, (Uri::create($uri))->getFragment());
    }

    public function getFragmentDataProvider(): array
    {
        return [
            'percent-encode spaces' => [
                'uri' => '/#f o',
                'expectedQuery' => 'f%20o',
            ],
            'do not encode plus' => [
                'uri' => '/#f+o',
                'expectedQuery' => 'f+o',
            ],
            'percent-encode multi-byte characters' => [
                'uri' => '/#€',
                'expectedQuery' => '%E2%82%AC',
            ],
            'do not double encode' => [
                'uri' => '/#f%20o',
                'expectedQuery' => 'f%20o',
            ],
            'percent-encode invalid percent encodings' => [
                'uri' => '/#f%2o',
                'expectedQuery' => 'f%252o',
            ],
            'do not encode path separators' => [
                'uri' => '#f/o',
                'expectedQuery' => 'f/o',
            ],
            'do not encode unreserved characters' => [
                'uri' => '/#' . self::UNRESERVED_CHARACTERS,
                'expectedQuery' => self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'uri' => '/#f%61r',
                'expectedQuery' => 'f%61r',
            ],
        ];
    }

    public function testWithScheme()
    {
        $httpUrl = Uri::create('http://example.com');
        $this->assertSame('http', $httpUrl->getScheme());

        $httpsUrl = $httpUrl->withScheme('https');
        $this->assertSame('https', $httpsUrl->getScheme());
        $this->assertNotSame($httpUrl, $httpsUrl);
        $this->assertUrisEqual($httpUrl, $httpsUrl, [self::URI_FIELD_SCHEME]);
    }

    public function testWithSchemeRemovesDefaultPort()
    {
        $httpUrl = Uri::create('http://example.com:443');
        $this->assertSame(443, $httpUrl->getPort());

        $httpsUrl = $httpUrl->withScheme('https');
        $this->assertNull($httpsUrl->getPort());
    }

    public function testWithUserInfo()
    {
        $uriWithoutUserInfo = Uri::create('http://example.com');
        $this->assertSame('', $uriWithoutUserInfo->getUserInfo());

        $uriWithUserOnly = $uriWithoutUserInfo->withUserInfo('user');
        $this->assertSame('user', $uriWithUserOnly->getUserInfo());
        $this->assertNotSame($uriWithoutUserInfo, $uriWithUserOnly);
        $this->assertUrisEqual($uriWithoutUserInfo, $uriWithUserOnly, [
            self::URI_FIELD_AUTHORITY,
            self::URI_FIELD_USERINFO,
        ]);

        $uriWithUserAndPassword = $uriWithUserOnly->withUserInfo('user-with-password', 'password');
        $this->assertNotSame($uriWithUserOnly, $uriWithUserAndPassword);
        $this->assertSame('user-with-password:password', $uriWithUserAndPassword->getUserInfo());

        $uriWithSameUserAndPassword = $uriWithUserAndPassword->withUserInfo('user-with-password', 'password');
        $this->assertSame($uriWithUserAndPassword, $uriWithSameUserAndPassword);

        $uriWithUserInfoRemoved = $uriWithUserAndPassword->withUserInfo('');
        $this->assertSame('', $uriWithUserInfoRemoved->getUserInfo());
    }

    public function testWithHost()
    {
        $uriWithOnlyPath = Uri::create('/path');
        $this->assertSame('', $uriWithOnlyPath->getHost());

        $uriWithPathAndHost = $uriWithOnlyPath->withHost('example.com');
        $this->assertSame('example.com', $uriWithPathAndHost->getHost());
        $this->assertNotSame($uriWithOnlyPath, $uriWithPathAndHost);
        $this->assertUrisEqual($uriWithOnlyPath, $uriWithPathAndHost, [
            self::URI_FIELD_AUTHORITY,
            self::URI_FIELD_HOST,
        ]);

        $uriWithSamePathAndHost = $uriWithPathAndHost->withHost('example.com');
        $this->assertSame($uriWithPathAndHost, $uriWithSamePathAndHost);

        $uriWithChangedHost = $uriWithSamePathAndHost->withHost('foo.example.com');
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
        $this->assertNull($httpUriWithDefaultPortAdded->getPort());
        $this->assertNotSame($httpUriWithoutPort, $httpUriWithDefaultPortAdded);
        $this->assertUrisEqual($httpUriWithDefaultPortAdded, $httpUriWithoutPort);

        $httpUriWithNonDefaultPort = $httpUriWithDefaultPortAdded->withPort(8080);
        $this->assertSame(8080, $httpUriWithNonDefaultPort->getPort());

        $httpUriWithSameNonDefaultPort = $httpUriWithNonDefaultPort->withPort(8080);
        $this->assertSame($httpUriWithNonDefaultPort, $httpUriWithSameNonDefaultPort);

        $httpUriWithPortRemoved = $httpUriWithNonDefaultPort->withPort(null);
        $this->assertNull($httpUriWithPortRemoved->getPort());
    }

    public function testWithPath()
    {
        $uriWithoutPath = Uri::create('http://example.com');
        $this->assertSame('', $uriWithoutPath->getPath());

        $uriWithPathAdded = $uriWithoutPath->withPath('/path');
        $this->assertSame('/path', $uriWithPathAdded->getPath());
        $this->assertNotSame($uriWithoutPath, $uriWithPathAdded);
        $this->assertUrisEqual($uriWithoutPath, $uriWithPathAdded, [self::URI_FIELD_PATH]);

        $uriWithSamePathAdded = $uriWithPathAdded->withPath('/path');
        $this->assertSame($uriWithPathAdded, $uriWithSamePathAdded);

        $uriWithPathRemoved = $uriWithSamePathAdded->withPath('');
        $this->assertSame('', $uriWithPathRemoved->getPath());
    }

    public function testWithQuery()
    {
        $uriWithoutQuery = Uri::create('http://example.com');
        $this->assertSame('', $uriWithoutQuery->getQuery());

        $uriWithQueryAdded = $uriWithoutQuery->withQuery('foo=bar');
        $this->assertSame('foo=bar', $uriWithQueryAdded->getQuery());
        $this->assertNotSame($uriWithoutQuery, $uriWithQueryAdded);
        $this->assertUrisEqual($uriWithoutQuery, $uriWithQueryAdded, [self::URI_FIELD_QUERY]);


        $uriWithSameQueryAdded = $uriWithQueryAdded->withQuery('foo=bar');
        $this->assertSame($uriWithQueryAdded, $uriWithSameQueryAdded);

        $uriWithQueryRemoved = $uriWithSameQueryAdded->withQuery('');
        $this->assertSame('', $uriWithQueryRemoved->getQuery());
    }

    public function testWithFragment()
    {
        $uriWithoutFragment = Uri::create('http://example.com');
        $this->assertSame('', $uriWithoutFragment->getFragment());

        $uriWithFragmentAdded = $uriWithoutFragment->withFragment('foo');
        $this->assertSame('foo', $uriWithFragmentAdded->getFragment());
        $this->assertNotSame($uriWithoutFragment, $uriWithFragmentAdded);
        $this->assertUrisEqual($uriWithoutFragment, $uriWithFragmentAdded, [self::URI_FIELD_FRAGMENT]);

        $uriWithFragmentRemoved = $uriWithFragmentAdded->withFragment('');
        $this->assertSame('', $uriWithFragmentRemoved->getFragment());
    }

    /**
     * @dataProvider toStringWithMutationDataProvider
     *
     * @param Uri $uri
     * @param string $expectedUri
     */
    public function testToStringWithMutation(Uri $uri, string $expectedUri)
    {
        $this->assertSame($expectedUri, (string) $uri);
    }

    public function toStringWithMutationDataProvider(): array
    {
        return [
            'fragment only' => [
                'uri' => new Uri('', '', '', null, '', '', 'fragment'),
                'expectedUrl' => '#fragment',
            ],
            'query only' => [
                'uri' => new Uri('', '', '', null, '', 'query', ''),
                'expectedUrl' => '?query',
            ],
            'path only' => [
                'uri' => new Uri('', '', '', null, '/path', '', ''),
                'expectedUrl' => '/path',
            ],
            'path only, starts with //' => [
                'uri' => new Uri('', '', '', null, '//path', '', ''),
                'expectedUrl' => '/path',
            ],
            'path and host, path does not start with /' => [
                'uri' => new Uri('', '', 'example.com', null, 'path', '', ''),
                'expectedUrl' => '//example.com/path',
            ],
        ];
    }

    /**
     * @dataProvider toStringDataProvider
     *
     * @param string $uri
     */
    public function testToString(string $uri)
    {
        $this->assertSame($uri, (string) Uri::create($uri));
    }

    public function toStringDataProvider(): array
    {
        return [
            'scheme' => [
                'uri' => 'file://',
            ],
            'scheme, host' => [
                'uri' => 'http://example.com',
            ],
            'scheme, user, host' => [
                'uri' => 'http://user@example.com',
            ],
            'scheme, password, host' => [
                'uri' => 'http://:password@example.com',
            ],
            'scheme, user, password, host' => [
                'uri' => 'http://user:password@example.com',
            ],
            'scheme, user, password, host, port' => [
                'uri' => 'http://user:password@example.com:8080',
            ],
            'scheme, user, password, host, port, path' => [
                'uri' => 'http://user:password@example.com:8080/path',
            ],
            'scheme, user, password, host, port, path, query' => [
                'uri' => 'http://user:password@example.com:8080/path?query',
            ],
            'scheme, user, password, host, port, path, query, fragment' => [
                'uri' => 'http://user:password@example.com:8080/path?query#fragment',
            ],
        ];
    }


    private function assertUrisEqual(UriInterface $expected, UriInterface $actual, array $exceptionFields = [])
    {
        if (in_array(self::URI_FIELD_AUTHORITY, $exceptionFields)) {
            $this->assertNotSame($expected->getAuthority(), $actual->getAuthority());
        } else {
            $this->assertSame($expected->getAuthority(), $actual->getAuthority());
        }

        if (in_array(self::URI_FIELD_FRAGMENT, $exceptionFields)) {
            $this->assertNotSame($expected->getFragment(), $actual->getFragment());
        } else {
            $this->assertSame($expected->getFragment(), $actual->getFragment());
        }

        if (in_array(self::URI_FIELD_HOST, $exceptionFields)) {
            $this->assertNotSame($expected->getHost(), $actual->getHost());
        } else {
            $this->assertSame($expected->getHost(), $actual->getHost());
        }

        if (in_array(self::URI_FIELD_PATH, $exceptionFields)) {
            $this->assertNotSame($expected->getPath(), $actual->getPath());
        } else {
            $this->assertSame($expected->getPath(), $actual->getPath());
        }

        if (in_array(self::URI_FIELD_PORT, $exceptionFields)) {
            $this->assertNotSame($expected->getPort(), $actual->getPort());
        } else {
            $this->assertSame($expected->getPort(), $actual->getPort());
        }

        if (in_array(self::URI_FIELD_QUERY, $exceptionFields)) {
            $this->assertNotSame($expected->getQuery(), $actual->getQuery());
        } else {
            $this->assertSame($expected->getQuery(), $actual->getQuery());
        }

        if (in_array(self::URI_FIELD_SCHEME, $exceptionFields)) {
            $this->assertNotSame($expected->getScheme(), $actual->getScheme());
        } else {
            $this->assertSame($expected->getScheme(), $actual->getScheme());
        }

        if (in_array(self::URI_FIELD_USERINFO, $exceptionFields)) {
            $this->assertNotSame($expected->getUserInfo(), $actual->getUserInfo());
        } else {
            $this->assertSame($expected->getUserInfo(), $actual->getUserInfo());
        }
    }
}
