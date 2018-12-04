<?php

namespace webignition\Url\Tests;

use IpUtils\Exception\InvalidExpressionException;
use webignition\Url\Host;

class HostTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $hostname
     */
    public function testCreate(string $hostname)
    {
        $host = new Host($hostname);

        $this->assertEquals($hostname, (string)$host);
    }

    public function createDataProvider(): array
    {
        return [
            'domain name' => [
                'hostname' => 'example.com',
            ],
            'IPv4' => [
                'hostname' => '192.168.0.1',
            ],
        ];
    }

    /**
     * @dataProvider getPartsDataProvider
     *
     * @param string $hostname
     * @param string[]|int[] $expectedParts
     */
    public function testGetParts(string $hostname, array $expectedParts)
    {
        $host = new Host($hostname);

        $this->assertEquals($expectedParts, $host->getParts());
    }

    public function getPartsDataProvider(): array
    {
        return [
            'foo' => [
                'hostname' => 'foo',
                'expectedParts' => [
                    'foo',
                ],
            ],
            'example.com' => [
                'hostname' => 'example.com',
                'expectedParts' => [
                    'example',
                    'com',
                ],
            ],
            'example.co.uk' => [
                'hostname' => 'example.co.uk',
                'expectedParts' => [
                    'example',
                    'co',
                    'uk',
                ],
            ],
            '192.168.0.1' => [
                'hostname' => '192.168.0.1',
                'expectedParts' => [
                    192,
                    168,
                    0,
                    1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider equalsDataProvider
     *
     * @param string $hostname
     * @param string $comparatorHostname
     * @param bool $expectedEquals
     */
    public function testEquals(string $hostname, string $comparatorHostname, bool $expectedEquals)
    {
        $host = new Host($hostname);
        $comparator = new Host($comparatorHostname);

        $this->assertEquals($expectedEquals, $host->equals($comparator));
        $this->assertEquals($expectedEquals, $comparator->equals($host));
    }

    public function equalsDataProvider(): array
    {
        return [
            'example.com == example.com' => [
                'hostname' => 'example.com',
                'comparatorHostname' => 'example.com',
                'expectedEquals' => true,
            ],
            'example.com != foo.example.com' => [
                'hostname' => 'example.com',
                'comparatorHostname' => 'foo.example.com',
                'expectedEquals' => false,
            ],
        ];
    }

    /**
     * @dataProvider isEquivalentToDataProvider
     *
     * @param string $hostname
     * @param string $comparatorHostname
     * @param string[] $excludeParts
     * @param bool $expectedIsEquivalentTo
     */
    public function testIsEquivalentTo(
        string $hostname,
        string $comparatorHostname,
        array $excludeParts,
        bool $expectedIsEquivalentTo
    ) {
        $host = new Host($hostname);
        $comparator = new Host($comparatorHostname);

        $this->assertEquals($expectedIsEquivalentTo, $host->isEquivalentTo($comparator, $excludeParts));
    }

    public function isEquivalentToDataProvider(): array
    {
        return [
            'example.com is equivalent to example.com' => [
                'hostname' => 'example.com',
                'comparatorHostname' => 'example.com',
                'excludeParts' => [],
                'expectedIsEquivalentTo' => true,
            ],
            'example.com is not equivalent to www.example.com' => [
                'hostname' => 'example.com',
                'comparatorHostname' => 'www.example.com',
                'excludeParts' => [],
                'expectedIsEquivalentTo' => false,
            ],
            'example.com is equivalent to www.example.com when excluding www part' => [
                'hostname' => 'example.com',
                'comparatorHostname' => 'www.example.com',
                'excludeParts' => [
                    'www',
                ],
                'expectedIsEquivalentTo' => true,
            ],
            'idn equivalence 1' => [
                'hostname' => 'econom.ía.com',
                'comparatorHostname' => 'econom.xn--a-iga.com',
                'excludeParts' => [],
                'expectedIsEquivalentTo' => true,
            ],
            'idn equivalence 2' => [
                'hostname' => 'ヒキワリ.ナットウ.ニホン',
                'comparatorHostname' => 'xn--nckwd5cta.xn--gckxcpg.xn--idk6a7d',
                'excludeParts' => [],
                'expectedIsEquivalentTo' => true,
            ],
            'idn equivalence 3' => [
                'hostname' => 'транспорт.com',
                'comparatorHostname' => 'xn--80a0addceeeh.com',
                'excludeParts' => [],
                'expectedIsEquivalentTo' => true,
            ],
        ];
    }

    /**
     * 0.0.0.0/8           "This" Network             RFC 1122, Section 3.2.1.3
     * 10.0.0.0/8          Private-Use Networks       RFC 1918
     * 127.0.0.0/8         Loopback                   RFC 1122, Section 3.2.1.3
     * 169.254.0.0/16      Link Local                 RFC 3927
     * 172.16.0.0/12       Private-Use Networks       RFC 1918
     * 192.0.0.0/24        IETF Protocol Assignments  RFC 5736
     * 192.0.2.0/24        TEST-NET-1                 RFC 5737
     * 192.88.99.0/24      6to4 Relay Anycast         RFC 3068
     * 192.168.0.0/16      Private-Use Networks       RFC 1918
     * 198.18.0.0/15       Network Interconnect Device Benchmark Testing   RFC 2544
     * 198.51.100.0/24     TEST-NET-2                 RFC 5737
     * 203.0.113.0/24      TEST-NET-3                 RFC 5737
     * 224.0.0.0/4         Multicast                  RFC 3171
     * 240.0.0.0/4         Reserved for Future Use    RFC 1112, Section 4
     * 255.255.255.255/32  Limited Broadcast          RFC 919, Section 7 RFC 922, Section 7
     *
     * @dataProvider ipRangeIsPubliclyRoutableDataProvider
     *
     * @param string $ipRange
     * @param bool $expectedIsPubliclyRoutable
     *
     * @throws InvalidExpressionException
     */
    public function testIpRangeIsPubliclyRoutable(string $ipRange, bool $expectedIsPubliclyRoutable)
    {
        $ipRangeSplit = explode('/', $ipRange);

        $startIp = $ipRangeSplit[0];
        $cidrRange = (int)$ipRangeSplit[1];
        $ipCount = 1 << (32 - $cidrRange);

        $firstIpInRange = ip2long($startIp);
        $lastIpInRange = $firstIpInRange + $ipCount - 1;

        $ipsToTest = array_merge(
            [
                $firstIpInRange,
                $lastIpInRange
            ],
            $this->getRandomLongIpSubsetInRange($firstIpInRange, $lastIpInRange)
        );

        foreach ($ipsToTest as $longIp) {
            $host = new Host(long2ip($longIp));

            $this->assertEquals($expectedIsPubliclyRoutable, $host->isPubliclyRoutable());
        }
    }

    /**
     * @throws InvalidExpressionException
     */
    public function testLoopbackIpIsNotPubliclyRoutable()
    {
        $host = new Host('127.0.0.1');

        $this->assertFalse($host->isPubliclyRoutable());
    }

    /**
     * @throws InvalidExpressionException
     */
    public function testDomainNameIsPubliclyRoutable()
    {
        $host = new Host('foo');

        $this->assertTrue($host->isPubliclyRoutable());
    }

    public function ipRangeIsPubliclyRoutableDataProvider(): array
    {
        return [
            '0.0.0.0/8 is not publicly routable' => [
                'ipRange' => '0.0.0.0/8',
                'expectedIsPubliclyRoutable' => false,
            ],
            '1.0.0.0/8 is publicly routable' => [
                'ipRange' => '1.0.0.0/8',
                'expectedIsPubliclyRoutable' => true,
            ],
            '2.0.0.0/8 is publicly routable' => [
                'ipRange' => '2.0.0.0/8',
                'expectedIsPubliclyRoutable' => true,
            ],
            '10.0.0.0/8 is not publicly routable' => [
                'ipRange' => '10.0.0.0/8',
                'expectedIsPubliclyRoutable' => false,
            ],
            '127.0.0.0/8 is not publicly routable' => [
                'ipRange' => '127.0.0.0/8',
                'expectedIsPubliclyRoutable' => false,
            ],
            '169.254.0.0/16 is not publicly routable' => [
                'ipRange' => '169.254.0.0/16',
                'expectedIsPubliclyRoutable' => false,
            ],
            '172.16.0.0/12 is not publicly routable' => [
                'ipRange' => '172.16.0.0/12',
                'expectedIsPubliclyRoutable' => false,
            ],
            '192.0.0.0/24 is not publicly routable' => [
                'ipRange' => '192.0.0.0/24',
                'expectedIsPubliclyRoutable' => false,
            ],
            '192.0.2.0/24 is not publicly routable' => [
                'ipRange' => '192.0.2.0/24',
                'expectedIsPubliclyRoutable' => false,
            ],
            '192.88.99.0/24 is not publicly routable' => [
                'ipRange' => '192.88.99.0/24',
                'expectedIsPubliclyRoutable' => false,
            ],
            '192.168.0.0/16 is not publicly routable' => [
                'ipRange' => '192.168.0.0/16',
                'expectedIsPubliclyRoutable' => false,
            ],
            '198.18.0.0/15 is not publicly routable' => [
                'ipRange' => '198.18.0.0/15',
                'expectedIsPubliclyRoutable' => false,
            ],
            '198.51.100.0/24 is not publicly routable' => [
                'ipRange' => '198.51.100.0/24',
                'expectedIsPubliclyRoutable' => false,
            ],
            '203.0.113.0/24 is not publicly routable' => [
                'ipRange' => '203.0.113.0/24',
                'expectedIsPubliclyRoutable' => false,
            ],
            '224.0.0.0/4 is not publicly routable' => [
                'ipRange' => '224.0.0.0/4',
                'expectedIsPubliclyRoutable' => false,
            ],
            '240.0.0.0/4 is not publicly routable' => [
                'ipRange' => '240.0.0.0/4',
                'expectedIsPubliclyRoutable' => false,
            ],
            '255.255.255.255/32 is not publicly routable' => [
                'ipRange' => '255.255.255.255/32',
                'expectedIsPubliclyRoutable' => false,
            ],
        ];
    }

    /**
     * @param int $first
     * @param int $last
     * @param int $max
     *
     * @return array
     */
    private function getRandomLongIpSubsetInRange(int $first, int $last, int $max = 32)
    {
        $ips = array();

        while (count($ips) < $max) {
            $ips[] = rand($first, $last);
        }

        return $ips;
    }
}
