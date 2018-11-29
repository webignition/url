<?php

namespace webignition\Tests\Url;

use IpUtils\Exception\InvalidExpressionException;
use webignition\Url\Query\Query;
use webignition\Url\Url;
use webignition\Url\UrlInterface;

class UrlTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $url = new Url('http://example.com');

        $this->assertEquals('http://example.com', (string)$url);
    }

    /**
     * @dataProvider getRootDataProvider
     *
     * @param Url $url
     * @param string $expectedRoot
     */
    public function testGetRoot(Url $url, string $expectedRoot)
    {
        $this->assertEquals($expectedRoot, $url->getRoot());
    }

    public function getRootDataProvider(): array
    {
        $punyCodeUrl = new Url('http://xn--g6h.example.com');
        $punyCodeUrl->getConfiguration()->enableConvertIdnToUtf8();

        return [
            'path is not present in root' => [
                'url' => new Url('http://example.com/foo'),
                'expectedRoot' => 'http://example.com',
            ],
            'has scheme, has host, has full credentials, has port' => [
                'url' => new Url('http://user:pass@example.com:8080'),
                'expectedRoot' => 'http://user:pass@example.com:8080',
            ],
            'has scheme, has host, has user, has port' => [
                'url' => new Url('http://:pass@example.com:8080'),
                'expectedRoot' => 'http://:pass@example.com:8080',
            ],
            'has scheme, has host, has pass, has port' => [
                'url' => new Url('http://user@example.com:8080'),
                'expectedRoot' => 'http://user@example.com:8080',
            ],
            'no scheme, has host, has full credentials, has port' => [
                'url' => new Url('//user:pass@example.com:8080'),
                'expectedRoot' => '//user:pass@example.com:8080',
            ],
            'host contains punycode; no conversion' => [
                'url' => new Url('http://xn--g6h.example.com'),
                'expectedRoot' => 'http://xn--g6h.example.com',
            ],
            'host contains non-ascii unicode; convert to ascii' => [
                'url' => $punyCodeUrl,
                'expectedRoot' => 'http://♥.example.com',
            ],
        ];
    }

    /**
     * @dataProvider hasCredentialsDataProvider
     *
     * @param Url $url
     * @param bool $expectedHas
     */
    public function testHasCredentials(Url $url, bool $expectedHas)
    {
        $this->assertEquals($expectedHas, $url->hasCredentials());
    }

    public function hasCredentialsDataProvider(): array
    {
        return [
            'no credentials' => [
                'url' => new Url('http://example.com/'),
                'expectedHas' => false,
            ],
            'has user' => [
                'url' => new Url('http://user@example.com/'),
                'expectedHas' => true,
            ],
            'has pass' => [
                'url' => new Url('http://:pass@example.com/'),
                'expectedHas' => true,
            ],
            'has user, has pass' => [
                'url' => new Url('http://user:pass@example.com/'),
                'expectedHas' => true,
            ],
        ];
    }

    /**
     * @dataProvider hasFragmentGetFragmentDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasFragment
     * @param string $expectedFragment
     */
    public function testHasFragmentGetFragment(Url $url, bool $expectedHasFragment, string $expectedFragment)
    {
        $this->assertEquals($expectedHasFragment, $url->hasFragment());
        $this->assertEquals($expectedFragment, $url->getFragment());
    }

    public function hasFragmentGetFragmentDataProvider(): array
    {
        return [
            'no fragment' => [
                'url' => new Url('http://example.com/'),
                'expectedHasFragment' => false,
                'expectedFragment' => '',
            ],
            'has fragment' => [
                'url' => new Url('http://example.com/#foo'),
                'expectedHasFragment' => true,
                'expectedFragment' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider hasHostGetHostDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasHost
     * @param string $expectedHost
     */
    public function testHasHostGetHost(Url $url, bool $expectedHasHost, string $expectedHost)
    {
        $this->assertEquals($expectedHasHost, $url->hasHost());
        $this->assertEquals($expectedHost, $url->getHost());
    }

    public function hasHostGetHostDataProvider(): array
    {
        return [
            'no host' => [
                'url' => new Url('/path'),
                'expectedHasHost' => false,
                'expectedHost' => '',
            ],
            'has host' => [
                'url' => new Url('//example.com/path'),
                'expectedHasHost' => true,
                'expectedHost' => 'example.com',
            ],
        ];
    }

    /**
     * @dataProvider hasPassGetPassDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasPass
     * @param string $expectedPass
     */
    public function testHasPassGetPass(Url $url, bool $expectedHasPass, string $expectedPass)
    {
        $this->assertEquals($expectedHasPass, $url->hasPass());
        $this->assertEquals($expectedPass, $url->getPass());
    }

    public function hasPassGetPassDataProvider(): array
    {
        return [
            'no pass' => [
                'url' => new Url('http://example.com/'),
                'expectedHasPass' => false,
                'expectedPass' => '',
            ],
            'has pass' => [
                'url' => new Url('http://:pass@example.com/'),
                'expectedHasPass' => true,
                'expectedPass' => 'pass',
            ],
        ];
    }

    /**
     * @dataProvider hasPathGetPathDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasPath
     * @param string $expectedPath
     */
    public function testHasPathGetPath(Url $url, bool $expectedHasPath, string $expectedPath)
    {
        $this->assertEquals($expectedHasPath, $url->hasPath());
        $this->assertEquals($expectedPath, $url->getPath());
    }

    public function hasPathGetPathDataProvider(): array
    {
        return [
            'no path' => [
                'url' => new Url('http://example.com'),
                'expectedHasPath' => false,
                'expectedPath' => '',
            ],
            'has path' => [
                'url' => new Url('http://example.com/path'),
                'expectedHasPath' => true,
                'expectedPath' => '/path',
            ],
        ];
    }

    /**
     * @dataProvider hasPortGetPortDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasPort
     * @param string $expectedPort
     */
    public function testHasPortGetPort(Url $url, bool $expectedHasPort, string $expectedPort)
    {
        $this->assertEquals($expectedHasPort, $url->hasPort());
        $this->assertEquals($expectedPort, $url->getPort());
    }

    public function hasPortGetPortDataProvider(): array
    {
        return [
            'no port' => [
                'url' => new Url('http://example.com'),
                'expectedHasPort' => false,
                'expectedPort' => '',
            ],
            'has port' => [
                'url' => new Url('http://example.com:8080'),
                'expectedHasPort' => true,
                'expectedPort' => 8080,
            ],
        ];
    }

    /**
     * @dataProvider hasQueryGetQueryDataProvider
     *
     * @param Url $url
     * @param string $expectedQuery
     */
    public function testHasQueryGetQuery(Url $url, string $expectedQuery)
    {
        $this->assertEquals($expectedQuery, $url->getQuery());
    }

    public function hasQueryGetQueryDataProvider(): array
    {
        return [
            'no query' => [
                'url' => new Url('http://example.com'),
                'expectedQuery' => '',
            ],
            'has query' => [
                'url' => new Url('http://example.com?foo=bar'),
                'expectedQuery' => 'foo=bar',
            ],
        ];
    }

    /**
     * @dataProvider hasSchemeGetSchemeDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasScheme
     * @param string $expectedScheme
     */
    public function testHasSchemeGetScheme(Url $url, bool $expectedHasScheme, string $expectedScheme)
    {
        $this->assertEquals($expectedHasScheme, $url->hasScheme());
        $this->assertEquals($expectedScheme, $url->getScheme());
    }

    public function hasSchemeGetSchemeDataProvider(): array
    {
        return [
            'no scheme' => [
                'url' => new Url('//example.com'),
                'expectedHasScheme' => false,
                'expectedScheme' => '',
            ],
            'has scheme' => [
                'url' => new Url('http://example.com'),
                'expectedHasScheme' => true,
                'expectedScheme' => 'http',
            ],
        ];
    }

    /**
     * @dataProvider hasUserGetUserDataProvider
     *
     * @param Url $url
     * @param bool $expectedHasUser
     * @param string $expectedUser
     */
    public function testHasUserGetUser(Url $url, bool $expectedHasUser, string $expectedUser)
    {
        $this->assertEquals($expectedHasUser, $url->hasUser());
        $this->assertEquals($expectedUser, $url->getUser());
    }

    public function hasUserGetUserDataProvider(): array
    {
        return [
            'no user' => [
                'url' => new Url('http://example.com'),
                'expectedHasUser' => false,
                'expectedUser' => '',
            ],
            'has user' => [
                'url' => new Url('http://user@example.com'),
                'expectedHasUser' => true,
                'expectedUser' => 'user',
            ],
        ];
    }

    /**
     * @dataProvider isAbsoluteIsProtocolRelativeIsRelativeDataProvider
     *
     * @param Url $url
     * @param bool $expectedIsAbsolute
     * @param bool $expectedIsProtocolRelative
     * @param bool $expectedIsRelative
     */
    public function testIsAbsoluteIsProtocolRelativeIsRelative(
        Url $url,
        bool $expectedIsAbsolute,
        bool $expectedIsProtocolRelative,
        bool $expectedIsRelative
    ) {
        $this->assertEquals($expectedIsAbsolute, $url->isAbsolute());
        $this->assertEquals($expectedIsProtocolRelative, $url->isProtocolRelative());
        $this->assertEquals($expectedIsRelative, $url->isRelative());
    }

    public function isAbsoluteIsProtocolRelativeIsRelativeDataProvider(): array
    {
        return [
            'absolute' => [
                'url' => new Url('http://example.com/foo/bar'),
                'expectedIsAbsolute' => true,
                'expectedIsProtocolRelative' => false,
                'expectedIsRelative' => false,
            ],
            'protocol-relative' => [
                'url' => new Url('//example.com/foo/bar'),
                'expectedIsAbsolute' => false,
                'expectedIsProtocolRelative' => true,
                'expectedIsRelative' => false,
            ],
            'relative' => [
                'url' => new Url('/foo/bar'),
                'expectedIsAbsolute' => false,
                'expectedIsProtocolRelative' => false,
                'expectedIsRelative' => true,
            ],
        ];
    }

    /**
     * @dataProvider setFragmentDataProvider
     *
     * @param Url $url
     * @param string|null $fragment
     * @param string $expectedUrl
     */
    public function testSetFragment(Url $url, ?string $fragment, string $expectedUrl)
    {
        $url->setFragment($fragment);

        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setFragmentDataProvider(): array
    {
        return [
            'no existing fragment; valid fragment lacking hash' => [
                'url' => new Url('http://example.com/'),
                'fragment' => 'foo',
                'expectedUrl' => 'http://example.com/#foo',
            ],
            'no existing fragment; valid fragment with hash' => [
                'url' => new Url('http://example.com/'),
                'fragment' => '#foo',
                'expectedUrl' => 'http://example.com/#foo',
            ],
            'has existing fragment; null fragment' => [
                'url' => new Url('http://example.com/#foo'),
                'fragment' => null,
                'expectedUrl' => 'http://example.com/',
            ],
            'has existing fragment; empty fragment' => [
                'url' => new Url('http://example.com/#foo'),
                'fragment' => '',
                'expectedUrl' => 'http://example.com/',
            ],
            'has existing fragment; whitespace fragment' => [
                'url' => new Url('http://example.com/#foo'),
                'fragment' => '   ',
                'expectedUrl' => 'http://example.com/',
            ],
            'has existing fragment; valid fragment lacking hash' => [
                'url' => new Url('http://example.com/#foo'),
                'fragment' => 'bar',
                'expectedUrl' => 'http://example.com/#bar',
            ],
            'has existing fragment; valid fragment with hash' => [
                'url' => new Url('http://example.com/#foo'),
                'fragment' => '#bar',
                'expectedUrl' => 'http://example.com/#bar',
            ],
        ];
    }

    /**
     * @dataProvider setPathDataProvider
     *
     * @param Url $url
     * @param string|null $path
     * @param string $expectedUrl
     */
    public function testSetPath(Url $url, ?string $path, string $expectedUrl)
    {
        $url->setPath($path);

        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setPathDataProvider(): array
    {
        return [
            'add to url without query and without fragment' => [
                'url' => new Url('http://example.com'),
                'path' => '/bar',
                'expectedUrl' => 'http://example.com/bar',
            ],
            'add to url with query' => [
                'url' => new Url('http://example.com?foo=bar'),
                'path' => '/foobar',
                'expectedUrl' => 'http://example.com/foobar?foo=bar',
            ],
            'add to url with fragment' => [
                'url' => new Url('http://example.com#foo'),
                'path' => '/foobar',
                'expectedUrl' => 'http://example.com/foobar#foo',
            ],
            'add to url with query and fragment' => [
                'url' => new Url('http://example.com?foo=bar#foo'),
                'path' => '/foobar',
                'expectedUrl' => 'http://example.com/foobar?foo=bar#foo',
            ],
            'replace existing path' => [
                'url' => new Url('http://example.com/foo'),
                'path' => '/bar',
                'expectedUrl' => 'http://example.com/bar',
            ],
            'remove existing path' => [
                'url' => new Url('http://example.com/foo'),
                'path' => null,
                'expectedUrl' => 'http://example.com',
            ],
            'remove existing path from url that has query and fragment' => [
                'url' => new Url('http://example.com/foo?query#fragment'),
                'path' => null,
                'expectedUrl' => 'http://example.com?query#fragment',
            ],
            'add path to hash-only url' => [
                'url' => new Url('#'),
                'path' => '/foo',
                'expectedUrl' => '/foo#',
            ],
            'add path to hash and identifier url' => [
                'url' => new Url('#bar'),
                'path' => '/foo',
                'expectedUrl' => '/foo#bar',
            ],
            'set path on url with plus characters in query' => [
                'url' => new Url('example.html?foo=++'),
                'path' => '/foo.html',
                'expectedUrl' => '/foo.html?foo=%2B%2B',
            ],
        ];
    }

    /**
     * @dataProvider setPortDataProvider
     *
     * @param Url $url
     * @param string|null $port
     * @param bool $expectedSucceeds
     * @param string $expectedUrl
     */
    public function testSetPort(Url $url, ?string $port, bool $expectedSucceeds, string $expectedUrl)
    {
        $succeeds = $url->setPort($port);

        $this->assertEquals($expectedSucceeds, $succeeds);
        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setPortDataProvider(): array
    {
        return [
            'remove existing port' => [
                'url' => new Url('http://example.com:8080'),
                'port' => null,
                'expectedSucceeds' => true,
                'expectedUrl' => 'http://example.com',
            ],
            'remove port when not set' => [
                'url' => new Url('http://example.com'),
                'port' => null,
                'expectedSucceeds' => true,
                'expectedUrl' => 'http://example.com',
            ],
            'invalid type: empty string' => [
                'url' => new Url('http://example.com'),
                'port' => '',
                'expectedSucceeds' => false,
                'expectedUrl' => 'http://example.com',
            ],
            'invalid type: non-numeric string' => [
                'url' => new Url('http://example.com'),
                'port' => 'foo',
                'expectedSucceeds' => false,
                'expectedUrl' => 'http://example.com',
            ],
            'added' => [
                'url' => new Url('http://example.com'),
                'port' => 9090,
                'expectedSucceeds' => true,
                'expectedUrl' => 'http://example.com:9090',
            ],
        ];
    }
    /**
     * @dataProvider setHostDataProvider
     *
     * @param Url $url
     * @param string|null $host
     * @param string $expectedUrl
     */
    public function testSetHost(Url $url, ?string $host, string $expectedUrl)
    {
        $url->setHost($host);

        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setHostDataProvider(): array
    {
        return [
            'no host, has relative path' => [
                'url' => new Url('file.extension'),
                'host' => 'example.com',
                'expectedUrl' => '//example.com/file.extension',
            ],
            'no host, has absolute path' => [
                'url' => new Url('/file.extension'),
                'host' => 'example.com',
                'expectedUrl' => '//example.com/file.extension',
            ],
            'has host' => [
                'url' => new Url('//example.com/foo'),
                'host' => 'bar.example.com',
                'expectedUrl' => '//bar.example.com/foo',
            ],
            'remove host from url that has path' => [
                'url' => new Url('//example.com/foo'),
                'host' => null,
                'expectedUrl' => '/foo',
            ],
            'remove host from url that has scheme, path' => [
                'url' => new Url('http://example.com/foo'),
                'host' => null,
                'expectedUrl' => '/foo',
            ],
            'remove host from url that has scheme, user, pass, port, path' => [
                'url' => new Url('http://user:pass@example.com:8080/foo'),
                'host' => null,
                'expectedUrl' => '/foo',
            ],
        ];
    }

    /**
     * @dataProvider setPassDataProvider
     *
     * @param Url $url
     * @param string|null $pass
     * @param bool $expectedSucceeds
     * @param string $expectedUrl
     */
    public function testSetPass(Url $url, ?string $pass, bool $expectedSucceeds, string $expectedUrl)
    {
        $succeeds = $url->setPass($pass);

        $this->assertEquals($expectedSucceeds, $succeeds);
        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setPassDataProvider(): array
    {
        return [
            'no host' => [
                'url' => new Url('file.extension'),
                'pass' => 'pass',
                'expectedSucceeds' => false,
                'expectedUrl' => 'file.extension',
            ],
            'has host' => [
                'url' => new Url('//example.com'),
                'pass' => 'pass',
                'expectedSucceeds' => true,
                'expectedUrl' => '//:pass@example.com',
            ],
            'has host, has user' => [
                'url' => new Url('//user@example.com'),
                'pass' => 'pass',
                'expectedSucceeds' => true,
                'expectedUrl' => '//user:pass@example.com',
            ],
            'has host, has user, has pass' => [
                'url' => new Url('//user:pass@example.com'),
                'pass' => 'new',
                'expectedSucceeds' => true,
                'expectedUrl' => '//user:new@example.com',
            ],
            'has host, has user, has pass; null pass' => [
                'url' => new Url('//user:pass@example.com'),
                'pass' => null,
                'expectedSucceeds' => true,
                'expectedUrl' => '//user@example.com',
            ],
            'has host, has user, has pass; empty pass' => [
                'url' => new Url('//user:pass@example.com'),
                'pass' => '',
                'expectedSucceeds' => true,
                'expectedUrl' => '//user:@example.com',
            ],
        ];
    }

    /**
     * @dataProvider setQueryDataProvider
     *
     * @param Url $url
     * @param string|null $query
     * @param string $expectedUrl
     */
    public function testSetQuery(Url $url, ?string $query, string $expectedUrl)
    {
        $url->setQuery($query);

        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setQueryDataProvider(): array
    {
        return [
            'no existing query, null query' => [
                'url' => new Url('//example.com'),
                'query' => null,
                'expectedUrl' => '//example.com',
            ],
            'no existing query, empty query' => [
                'url' => new Url('//example.com'),
                'query' => '',
                'expectedUrl' => '//example.com',
            ],
            'no existing query, whitespace query' => [
                'url' => new Url('//example.com'),
                'query' => '   ',
                'expectedUrl' => '//example.com',
            ],
            'existing query, null query' => [
                'url' => new Url('//example.com?foo=bar'),
                'query' => null,
                'expectedUrl' => '//example.com',
            ],
            'existing query, empty query' => [
                'url' => new Url('//example.com?foo=bar'),
                'query' => '',
                'expectedUrl' => '//example.com',
            ],
            'existing query, whitespace query' => [
                'url' => new Url('//example.com?foo=bar'),
                'query' => '   ',
                'expectedUrl' => '//example.com',
            ],
            'existing query, query without question mark' => [
                'url' => new Url('//example.com?foo=bar'),
                'query' => 'bar=foobar',
                'expectedUrl' => '//example.com?bar=foobar',
            ],
            'existing query, query with question mark' => [
                'url' => new Url('//example.com?foo=bar'),
                'query' => '?bar=foobar',
                'expectedUrl' => '//example.com?bar=foobar',
            ],
        ];
    }

    /**
     * @dataProvider setSchemeDataProvider
     *
     * @param Url $url
     * @param string|null $scheme
     * @param string $expectedUrl
     */
    public function testSetScheme(Url $url, ?string $scheme, string $expectedUrl)
    {
        $url->setScheme($scheme);

        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setSchemeDataProvider(): array
    {
        return [
            'no existing scheme, null scheme' => [
                'url' => new Url('//example.com'),
                'scheme' => null,
                'expectedUrl' => '//example.com',
            ],
            'existing scheme, null scheme' => [
                'url' => new Url('http://example.com'),
                'scheme' => null,
                'expectedUrl' => '//example.com',
            ],
            'schemeless url' => [
                'url' => new Url('example.com'),
                'scheme' => 'http',
                'expectedUrl' => 'http://example.com',
            ],
            'protocol-relative url' => [
                'url' => new Url('//example.com'),
                'scheme' => 'http',
                'expectedUrl' => 'http://example.com',
            ],
            'absolute url' => [
                'url' => new Url('https://example.com'),
                'scheme' => 'http',
                'expectedUrl' => 'http://example.com',
            ],
        ];
    }

    /**
     * @dataProvider setUserDataProvider
     *
     * @param Url $url
     * @param string|null $user
     * @param bool $expectedSucceeds
     * @param string $expectedUrl
     */
    public function testSetUser(Url $url, ?string $user, bool $expectedSucceeds, string $expectedUrl)
    {
        $succeeds = $url->setUser($user);

        $this->assertEquals($expectedSucceeds, $succeeds);
        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setUserDataProvider(): array
    {
        return [
            'cannot set user on url with no host' => [
                'url' => new Url('/path'),
                'user' => 'user',
                'expectedSucceeds' => false,
                'expectedUrl' => '/path',
            ],
            'no existing user, null user, no pass' => [
                'url' => new Url('//example.com/path'),
                'user' => null,
                'expectedSucceeds' => true,
                'expectedUrl' => '//example.com/path',
            ],
            'no existing user, empty user, no pass' => [
                'url' => new Url('//example.com/path'),
                'user' => '',
                'expectedSucceeds' => true,
                'expectedUrl' => '//@example.com/path',
            ],
            'empty existing user, empty user, has pass' => [
                'url' => new Url('//:pass@example.com/path'),
                'user' => '',
                'expectedSucceeds' => true,
                'expectedUrl' => '//:pass@example.com/path',
            ],
            'existing user, null user, no pass' => [
                'url' => new Url('//user@example.com/path'),
                'user' => null,
                'expectedSucceeds' => true,
                'expectedUrl' => '//example.com/path',
            ],
            'existing user, empty user, no pass' => [
                'url' => new Url('//user@example.com/path'),
                'user' => '',
                'expectedSucceeds' => true,
                'expectedUrl' => '//@example.com/path',
            ],
            'existing user, empty user, has pass' => [
                'url' => new Url('//user:pass@example.com/path'),
                'user' => '',
                'expectedSucceeds' => true,
                'expectedUrl' => '//:pass@example.com/path',
            ],
            'existing user, new user, no pass' => [
                'url' => new Url('//user@example.com/path'),
                'user' => 'new',
                'expectedSucceeds' => true,
                'expectedUrl' => '//new@example.com/path',
            ],
            'existing user, new user, has pass' => [
                'url' => new Url('//user:pass@example.com/path'),
                'user' => 'new',
                'expectedSucceeds' => true,
                'expectedUrl' => '//new:pass@example.com/path',
            ],
        ];
    }

    /**
     * @dataProvider setPartDataProvider
     *
     * @param Url $url
     * @param string $partName
     * @param string|int $value
     * @param bool $expectedSucceeds
     * @param string $expectedUrl
     */
    public function testSetPart(Url $url, string $partName, $value, bool $expectedSucceeds, string $expectedUrl)
    {
        $succeeds = $url->setPart($partName, $value);

        $this->assertEquals($expectedSucceeds, $succeeds);
        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function setPartDataProvider(): array
    {
        $dataSet = [
            'unknown part' => [
                'url' => new Url('http://example.com/path'),
                'partName' => 'foo',
                'value' => 'bar',
                'expectedSucceeds' => false,
                'expectedUrl' =>'http://example.com/path',
            ],
        ];

        foreach ($this->setSchemeDataProvider() as $index => $testData) {
            $dataSet['scheme: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_SCHEME,
                'value' => $testData['scheme'],
                'expectedSucceeds' => true,
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setUserDataProvider() as $index => $testData) {
            $dataSet['user: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_USER,
                'value' => $testData['user'],
                'expectedSucceeds' => $testData['expectedSucceeds'],
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setPassDataProvider() as $index => $testData) {
            $dataSet['pass: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_PASS,
                'value' => $testData['pass'],
                'expectedSucceeds' => $testData['expectedSucceeds'],
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setHostDataProvider() as $index => $testData) {
            $dataSet['host: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_HOST,
                'value' => $testData['host'],
                'expectedSucceeds' => true,
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setPortDataProvider() as $index => $testData) {
            $dataSet['port: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_PORT,
                'value' => $testData['port'],
                'expectedSucceeds' => $testData['expectedSucceeds'],
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setPathDataProvider() as $index => $testData) {
            $dataSet['path: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_PATH,
                'value' => $testData['path'],
                'expectedSucceeds' => true,
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setQueryDataProvider() as $index => $testData) {
            $dataSet['query: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_QUERY,
                'value' => $testData['query'],
                'expectedSucceeds' => true,
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        foreach ($this->setFragmentDataProvider() as $index => $testData) {
            $dataSet['fragment: ' . $index] = [
                'url' => $testData['url'],
                'partName' => UrlInterface::PART_FRAGMENT,
                'value' => $testData['fragment'],
                'expectedSucceeds' => true,
                'expectedUrl' => $testData['expectedUrl'],
            ];
        }

        return $dataSet;
    }

    /**
     * When setting a fragment to null in a url that had a fragment
     * and then setting the query to null where there was no query
     * was resulting in the fragment containing the string '?', this is incorrect
     */
    public function testReplaceFragmentWithNullSetNullQuery()
    {
        $url = new Url('http://example.com/#fragment');
        $url->setFragment(null);
        $url->setQuery(null);

        $this->assertNull($url->getFragment());

        $query = $url->getQuery();

        $this->assertInstanceOf(Query::class, $query);
        $this->assertTrue($query->isEmpty());

        $this->assertEquals('http://example.com/', (string)$url);
    }

    /**
     * @dataProvider toStringDataProvider
     *
     * @param Url $url
     * @param string $expectedStringUrl
     */
    public function testToString(Url $url, string $expectedStringUrl)
    {
        $this->assertEquals($expectedStringUrl, (string)$url);
    }

    public function toStringDataProvider(): array
    {
        return [
            'hash only' => [
                'url' => new Url('#'),
                'expectedStringUrl' => '#',
            ],
            'hash and fragment' => [
                'url' => new Url('#foo'),
                'expectedStringUrl' => '#foo',
            ],
            'key only query' => [
                'url' => new Url('?key'),
                'expectedStringUrl' => '?key',
            ],
        ];
    }

    /**
     * @dataProvider getQueryDataProvider
     *
     * @param Url $url
     * @param string $expectedQueryString
     * @param string $expectedUrl
     */
    public function testGetQuery(Url $url, string $expectedQueryString, string $expectedUrl)
    {
        $query = $url->getQuery();

        $this->assertInstanceOf(Query::class, $url->getQuery());
        $this->assertEquals($expectedQueryString, (string)$query);
        $this->assertEquals($expectedUrl, (string)$url);
    }

    public function getQueryDataProvider(): array
    {
        return [
            'no query' => [
                'url' => new Url('//example.com'),
                'expectedQueryString' => '',
                'expectedUrl' => '//example.com',
            ],
            'has query' => [
                'url' => new Url('//example.com?foo=bar'),
                'expectedQueryString' => 'foo=bar',
                'expectedUrl' => '//example.com?foo=bar',
            ],
        ];
    }

    /**
     * @dataProvider isPubliclyRoutableDataProvider
     *
     * @param string $url
     * @param bool $expectedIsPubliclyRoutable
     *
     * @throws InvalidExpressionException
     */
    public function testIsPubliclyRoutable(string $url, bool $expectedIsPubliclyRoutable)
    {
        $urlObject = new Url($url);

        $this->assertEquals($expectedIsPubliclyRoutable, $urlObject->isPubliclyRoutable());
    }

    public function isPubliclyRoutableDataProvider(): array
    {
        return [
            'no host' => [
                'url' => 'example',
                'expectedIsPubliclyRoutable' => false,
            ],
            'host not publicly routable' => [
                'url' => 'http://127.0.0.1',
                'expectedIsPubliclyRoutable' => false,
            ],
            'host lacks dots' => [
                'url' => 'http://example',
                'expectedIsPubliclyRoutable' => false,
            ],
            'host starts with dot' => [
                'url' => 'http://.example',
                'expectedIsPubliclyRoutable' => false,
            ],
            'host ends with dot' => [
                'url' => 'http://example.',
                'expectedIsPubliclyRoutable' => false,
            ],
            'valid' => [
                'url' => 'http://example.com',
                'expectedIsPubliclyRoutable' => true,
            ],
        ];
    }

    /**
     * @dataProvider removeUserRemovePassDataProvider
     *
     * @param UrlInterface $url
     * @param UrlInterface $expectedUrl
     */
    public function testRemoveUserRemovePass(UrlInterface $url, UrlInterface $expectedUrl)
    {
        $url->setUser(null);
        $url->setPass(null);

        $this->assertEquals((string) $expectedUrl, (string) $url);
    }

    public function removeUserRemovePassDataProvider(): array
    {
        return [
            'no user info' => [
                'url' => new Url('http://example.com'),
                'expectedUrl' => new Url('http://example.com'),
            ],
            'has user' => [
                'url' => new Url('http://user@example.com'),
                'expectedUrl' => new Url('http://example.com'),
            ],
            'has pass' => [
                'url' => new Url('http://:pass@example.com'),
                'expectedUrl' => new Url('http://example.com'),
            ],
            'has user, has pass' => [
                'url' => new Url('http://user:pass@example.com'),
                'expectedUrl' => new Url('http://example.com'),
            ],
        ];
    }
}
