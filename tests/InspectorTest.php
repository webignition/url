<?php

namespace webignition\Url\Tests;

use IpUtils\Exception\InvalidExpressionException;
use Psr\Http\Message\UriInterface;
use webignition\Url\Inspector;
use webignition\Url\Url;

class InspectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isNotPubliclyRoutableDataProvider
     *
     * @param UriInterface $url
     * @param bool $expectedIsPubliclyRoutable
     *
     * @throws InvalidExpressionException
     */
    public function testIsNotPubliclyRoutable(UriInterface $url, bool $expectedIsPubliclyRoutable)
    {
        $this->assertEquals($expectedIsPubliclyRoutable, Inspector::isNotPubliclyRoutable($url));
    }

    public function isNotPubliclyRoutableDataProvider(): array
    {
        return [
            'no host' => [
                'url' => new Url('example'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host not publicly routable' => [
                'url' => new Url('http://127.0.0.1'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host lacks dots' => [
                'url' => new Url('http://example'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host starts with dot' => [
                'url' => new Url('http://.example'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host ends with dot' => [
                'url' => new Url('http://example.'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'valid' => [
                'url' => new Url('http://example.com'),
                'expectedIsPubliclyRoutable' => false,
            ],
        ];
    }

    /**
     * @dataProvider isProtocolRelativeDataProvider
     *
     * @param UriInterface $uri
     * @param bool $expectedIsProtocolRelative
     */
    public function testIsProtocolRelative(UriInterface $uri, bool $expectedIsProtocolRelative)
    {
        $this->assertSame($expectedIsProtocolRelative, Inspector::isProtocolRelative($uri));
    }

    public function isProtocolRelativeDataProvider(): array
    {
        return [
            'no scheme, no host is not protocol relative' => [
                'uri' => new Url('/path'),
                'expectedIsProtocolRelative' => false,
            ],
            'has scheme is not protocol relative' => [
                'uri' => new Url('http://example'),
                'expectedIsProtocolRelative' => false,
            ],
            'no scheme has host is protocol relative' => [
                'uri' => new Url('//example.com'),
                'expectedIsProtocolRelative' => true,
            ],
        ];
    }
}
