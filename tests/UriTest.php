<?php

namespace webignition\Url\Tests;

use webignition\Url\Filter;
use webignition\Url\Url;

class UriTest extends \PHPUnit\Framework\TestCase
{
    const UNRESERVED_CHARACTERS = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';
    const GEN_DELIMITERS = ':/?#[]@';
    const SUB_DELIMITERS = '/!$&\'()*+,;=';

    public function testCreateWithInvalidPort()
    {
        $this->expectException(\InvalidArgumentException::class);

        Url::create('http://example.com:' . (Filter::MIN_PORT - 1));
    }

    /**
     * @dataProvider getSchemeDataProvider
     *
     * @param string $scheme
     * @param string $expectedScheme
     */
    public function testGetScheme(string $scheme, string $expectedScheme)
    {
        $uri = new Url($scheme, '', '', null, '', '', '');

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
        $this->assertSame($expectedAuthority, (Url::create($uri))->getAuthority());
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
        $this->assertSame($expectedUserInfo, (Url::create($uri))->getUserInfo());
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
        $this->assertSame($expectedHost, (Url::create($uri))->getHost());
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
        $this->assertSame($expectedPort, (Url::create($uri))->getPort());
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
     * @dataProvider getPathGetQueryGetFragmentDataProvider
     *
     * @param string $uri
     * @param string $expectedPath
     * @param string $expectedQuery
     * @param string $expectedFragment
     */
    public function testGetPathGetQueryGetFragment(
        string $uri,
        string $expectedPath,
        string $expectedQuery,
        string $expectedFragment
    ) {
        $uriObject = Url::create($uri);

        $this->assertSame($expectedPath, $uriObject->getPath());
        $this->assertSame($expectedQuery, $uriObject->getQuery());
        $this->assertSame($expectedFragment, $uriObject->getFragment());
    }

    public function getPathGetQueryGetFragmentDataProvider(): array
    {
        return [
            'empty' => [
                'uri' => '',
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'relative path' => [
                'uri' => 'path',
                'expectedPath' => 'path',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'absolute path' => [
                'uri' => '/path',
                'expectedPath' => '/path',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'query' => [
                'uri' => '?query',
                'expectedPath' => '',
                'expectedQuery' => 'query',
                'expectedFragment' => '',
            ],
            'fragment' => [
                'uri' => '#fragment',
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => 'fragment',
            ],
            'full url' => [
                'uri' => 'http://example.com/path?query#fragment',
                'expectedPath' => '/path',
                'expectedQuery' => 'query',
                'expectedFragment' => 'fragment',
            ],
        ];
    }

    public function testWithScheme()
    {
        $httpUrl = Url::create('http://example.com');
        $this->assertSame('http', $httpUrl->getScheme());

        $httpsUrl = $httpUrl->withScheme('https');
        $this->assertSame('https', $httpsUrl->getScheme());
        $this->assertNotSame($httpUrl, $httpsUrl);
        $this->assertSame('https://example.com', (string) $httpsUrl);
    }

    public function testWithSchemeRemovesDefaultPort()
    {
        $httpUrl = Url::create('http://example.com:443');
        $this->assertSame(443, $httpUrl->getPort());

        $httpsUrl = $httpUrl->withScheme('https');
        $this->assertNull($httpsUrl->getPort());
    }

    public function testWithUserInfo()
    {
        $uriWithoutUserInfo = Url::create('http://example.com');
        $this->assertSame('', $uriWithoutUserInfo->getUserInfo());

        $uriWithUserOnly = $uriWithoutUserInfo->withUserInfo('user');
        $this->assertSame('user', $uriWithUserOnly->getUserInfo());
        $this->assertNotSame($uriWithoutUserInfo, $uriWithUserOnly);
        $this->assertSame(
            'http://user@example.com',
            (string) $uriWithUserOnly
        );

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
        $uriWithOnlyPath = Url::create('/path');
        $this->assertSame('', $uriWithOnlyPath->getHost());

        $uriWithPathAndHost = $uriWithOnlyPath->withHost('example.com');
        $this->assertSame('example.com', $uriWithPathAndHost->getHost());
        $this->assertNotSame($uriWithOnlyPath, $uriWithPathAndHost);
        $this->assertSame('//example.com/path', (string) $uriWithPathAndHost);

        $uriWithSamePathAndHost = $uriWithPathAndHost->withHost('example.com');
        $this->assertSame($uriWithPathAndHost, $uriWithSamePathAndHost);

        $uriWithChangedHost = $uriWithSamePathAndHost->withHost('foo.example.com');
        $this->assertSame('foo.example.com', $uriWithChangedHost->getHost());

        $uriWithRemovedHost = $uriWithPathAndHost->withHost('');
        $this->assertSame('', $uriWithRemovedHost->getHost());
    }

    public function testWithPortInvalidPort()
    {
        $uri = Url::create('http://example.com/');

        $this->expectException(\InvalidArgumentException::class);

        $uri->withPort(Filter::MIN_PORT - 1);
    }

    public function testWithPort()
    {
        $httpUriWithoutPort = Url::create('http://example.com');
        $this->assertNull($httpUriWithoutPort->getPort());

        $httpUriWithDefaultPortAdded = $httpUriWithoutPort->withPort(80);
        $this->assertNull($httpUriWithDefaultPortAdded->getPort());
        $this->assertNotSame($httpUriWithoutPort, $httpUriWithDefaultPortAdded);
        $this->assertSame('http://example.com', (string) $httpUriWithDefaultPortAdded);

        $httpUriWithNonDefaultPort = $httpUriWithDefaultPortAdded->withPort(8080);
        $this->assertSame(8080, $httpUriWithNonDefaultPort->getPort());

        $httpUriWithSameNonDefaultPort = $httpUriWithNonDefaultPort->withPort(8080);
        $this->assertSame($httpUriWithNonDefaultPort, $httpUriWithSameNonDefaultPort);

        $httpUriWithPortRemoved = $httpUriWithNonDefaultPort->withPort(null);
        $this->assertNull($httpUriWithPortRemoved->getPort());
    }

    public function testWithPath()
    {
        $uriWithoutPath = Url::create('http://example.com');
        $this->assertSame('', $uriWithoutPath->getPath());

        $uriWithPathAdded = $uriWithoutPath->withPath('/path');
        $this->assertSame('/path', $uriWithPathAdded->getPath());
        $this->assertNotSame($uriWithoutPath, $uriWithPathAdded);
        $this->assertSame('http://example.com/path', (string) $uriWithPathAdded);

        $uriWithSamePathAdded = $uriWithPathAdded->withPath('/path');
        $this->assertSame($uriWithPathAdded, $uriWithSamePathAdded);

        $uriWithPathRemoved = $uriWithSamePathAdded->withPath('');
        $this->assertSame('', $uriWithPathRemoved->getPath());
    }

    public function testWithQuery()
    {
        $uriWithoutQuery = Url::create('http://example.com');
        $this->assertSame('', $uriWithoutQuery->getQuery());

        $uriWithQueryAdded = $uriWithoutQuery->withQuery('foo=bar');
        $this->assertSame('foo=bar', $uriWithQueryAdded->getQuery());
        $this->assertNotSame($uriWithoutQuery, $uriWithQueryAdded);
        $this->assertSame('http://example.com?foo=bar', (string) $uriWithQueryAdded);

        $uriWithSameQueryAdded = $uriWithQueryAdded->withQuery('foo=bar');
        $this->assertSame($uriWithQueryAdded, $uriWithSameQueryAdded);

        $uriWithQueryRemoved = $uriWithSameQueryAdded->withQuery('');
        $this->assertSame('', $uriWithQueryRemoved->getQuery());
    }

    public function testWithFragment()
    {
        $uriWithoutFragment = Url::create('http://example.com');
        $this->assertSame('', $uriWithoutFragment->getFragment());

        $uriWithFragmentAdded = $uriWithoutFragment->withFragment('fragment');
        $this->assertSame('fragment', $uriWithFragmentAdded->getFragment());
        $this->assertNotSame($uriWithoutFragment, $uriWithFragmentAdded);
        $this->assertSame('http://example.com#fragment', (string) $uriWithFragmentAdded);

        $uriWithFragmentRemoved = $uriWithFragmentAdded->withFragment('');
        $this->assertSame('', $uriWithFragmentRemoved->getFragment());
    }

    /**
     * @dataProvider toStringWithMutationDataProvider
     *
     * @param Url $uri
     * @param string $expectedUri
     */
    public function testToStringWithMutation(Url $uri, string $expectedUri)
    {
        $this->assertSame($expectedUri, (string) $uri);
    }

    public function toStringWithMutationDataProvider(): array
    {
        return [
            'fragment only' => [
                'uri' => new Url('', '', '', null, '', '', 'fragment'),
                'expectedUrl' => '#fragment',
            ],
            'query only' => [
                'uri' => new Url('', '', '', null, '', 'query', ''),
                'expectedUrl' => '?query',
            ],
            'path only' => [
                'uri' => new Url('', '', '', null, '/path', '', ''),
                'expectedUrl' => '/path',
            ],
            'path only, starts with //' => [
                'uri' => new Url('', '', '', null, '//path', '', ''),
                'expectedUrl' => '/path',
            ],
            'path and host, path does not start with /' => [
                'uri' => new Url('', '', 'example.com', null, 'path', '', ''),
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
        $this->assertSame($uri, (string) Url::create($uri));
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

    /**
     * @dataProvider encodingOfGenAndSubDelimitersDataProvider
     *
     * @param string $uri
     * @param string $expectedPath
     * @param string $expectedQuery
     * @param string $expectedFragment
     */
    public function testEncodingOfGenAndSubDelimiters(
        string $uri,
        string $expectedPath,
        string $expectedQuery,
        string $expectedFragment
    ) {
        $uriObject = Url::create($uri);

        $this->assertSame($expectedPath, $uriObject->getPath());
        $this->assertSame($expectedQuery, $uriObject->getQuery());
        $this->assertSame($expectedFragment, $uriObject->getFragment());
        $this->assertSame($uri, (string) $uri);
    }

    public function encodingOfGenAndSubDelimitersDataProvider(): array
    {
        return [
            'no path, no query, no fragment' => [
                'uri' => 'http://example.com',
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'sub-delimiters in path' => [
                'uri' => 'http://example.com/' . self::SUB_DELIMITERS,
                'expectedPath' => '/' . self::SUB_DELIMITERS,
                'expectedQuery' => '',
                'expectedFragment' => '',
            ],
            'sub-delimiters in query' => [
                'uri' => 'http://example.com?' . self::SUB_DELIMITERS,
                'expectedPath' => '',
                'expectedQuery' => self::SUB_DELIMITERS,
                'expectedFragment' => '',
            ],
            'sub-delimiters in fragment' => [
                'uri' => 'http://example.com#' . self::SUB_DELIMITERS,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => self::SUB_DELIMITERS,
            ],
            'sub-delimiters in path, query, fragment' => [
                'uri' => sprintf(
                    'http://example.com/%s?%s#%s',
                    self::SUB_DELIMITERS,
                    self::SUB_DELIMITERS,
                    self::SUB_DELIMITERS
                ),
                'expectedPath' => '/' . self::SUB_DELIMITERS,
                'expectedQuery' => self::SUB_DELIMITERS,
                'expectedFragment' => self::SUB_DELIMITERS,
            ],
            'gen-delimiters in fragment' => [
                'uri' => 'http://example.com?#' . self::GEN_DELIMITERS,
                'expectedPath' => '',
                'expectedQuery' => '',
                'expectedFragment' => ':/?%23%5B%5D@',
            ],
        ];
    }
}
