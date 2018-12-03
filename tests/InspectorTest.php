<?php

namespace webignition\Url\Tests;

use IpUtils\Exception\InvalidExpressionException;
use Psr\Http\Message\UriInterface;
use webignition\Url\Inspector;
use webignition\Url\Uri;

class InspectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isPubliclyRoutableDataProvider
     *
     * @param UriInterface $url
     * @param bool $expectedIsPubliclyRoutable
     *
     * @throws InvalidExpressionException
     */
    public function testIsPubliclyRoutable(UriInterface $url, bool $expectedIsPubliclyRoutable)
    {
        $inspector = new Inspector();

        $this->assertEquals($expectedIsPubliclyRoutable, $inspector->isPubliclyRoutable($url));
    }

    public function isPubliclyRoutableDataProvider(): array
    {
        return [
            'no host' => [
                'url' => Uri::create('example'),
                'expectedIsPubliclyRoutable' => false,
            ],
            'host not publicly routable' => [
                'url' => Uri::create('http://127.0.0.1'),
                'expectedIsPubliclyRoutable' => false,
            ],
            'host lacks dots' => [
                'url' => Uri::create('http://example'),
                'expectedIsPubliclyRoutable' => false,
            ],
            'host starts with dot' => [
                'url' => Uri::create('http://.example'),
                'expectedIsPubliclyRoutable' => false,
            ],
            'host ends with dot' => [
                'url' => Uri::create('http://example.'),
                'expectedIsPubliclyRoutable' => false,
            ],
            'valid' => [
                'url' => Uri::create('http://example.com'),
                'expectedIsPubliclyRoutable' => true,
            ],
        ];
    }
}
