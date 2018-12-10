<?php

namespace webignition\Url\Tests;

use webignition\Url\DefaultPortIdentifier;

class DefaultPortIdentifierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isDefaultPortDataProvider
     *
     * @param string|null $scheme
     * @param int|null $port
     * @param bool $expectedIsDefaultPort
     */
    public function testIsDefaultPort(?string $scheme, ?int $port, bool $expectedIsDefaultPort)
    {
        $this->assertSame($expectedIsDefaultPort, DefaultPortIdentifier::isDefaultPort($scheme, $port));
    }

    public function isDefaultPortDataProvider(): array
    {
        return [
            'scheme: null, port: x' => [
                'scheme' => null,
                'port' => 80,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: x, port: null' => [
                'scheme' => 'http',
                'port' => null,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: http, port: 80' => [
                'scheme' => 'http',
                'port' => 80,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: http, port: 999' => [
                'scheme' => 'http',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: https, port: 443' => [
                'scheme' => 'https',
                'port' => 443,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: https, port: 999' => [
                'scheme' => 'https',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: ftp, port: 21' => [
                'scheme' => 'ftp',
                'port' => 21,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: ftp, port: 999' => [
                'scheme' => 'ftp',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: gopher, port: 70' => [
                'scheme' => 'gopher',
                'port' => 70,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: gopher, port: 999' => [
                'scheme' => 'gopher',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: nntp, port: 119' => [
                'scheme' => 'nntp',
                'port' => 119,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: nntp, port: 999' => [
                'scheme' => 'nntp',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: news, port: 119' => [
                'scheme' => 'news',
                'port' => 119,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: news, port: 999' => [
                'scheme' => 'news',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: telnet, port: 23' => [
                'scheme' => 'telnet',
                'port' => 23,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: telnet, port: 999' => [
                'scheme' => 'telnet',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: tn3270, port: 23' => [
                'scheme' => 'tn3270',
                'port' => 23,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: tn3270, port: 999' => [
                'scheme' => 'tn3270',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: imap, port: 143' => [
                'scheme' => 'imap',
                'port' => 143,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: imap, port: 999' => [
                'scheme' => 'imap',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: pop, port: 110' => [
                'scheme' => 'pop',
                'port' => 110,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: pop, port: 999' => [
                'scheme' => 'pop',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
            'scheme: ldap, port: 389' => [
                'scheme' => 'ldap',
                'port' => 389,
                'expectedIsDefaultPort' => true,
            ],
            'scheme: ldap, port: 999' => [
                'scheme' => 'ldap',
                'port' => 999,
                'expectedIsDefaultPort' => false,
            ],
        ];
    }
}
