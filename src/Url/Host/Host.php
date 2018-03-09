<?php

namespace webignition\Url\Host;

use Etechnika\IdnaConvert\IdnaConvert;
use IpUtils\Address\IPv4;
use IpUtils\Exception\InvalidExpressionException;
use IpUtils\Expression\Subnet;
use IpUtils\Factory as IpUtilsFactory;

/**
 * Represents the host part of a URL
 */
class Host
{
    const HOST_PART_SEPARATOR = '.';

    const UNROUTABLE_THIS_NETWORK_RANGE = '0.0.0.0/8';
    const UNROUTABLE_PRIVATE_USE_NETWORKS_10_RANGE = '10.0.0.0/8';
    const UNROUTABLE_LOOPBACK_RANGE = '127.0.0.0/8';
    const UNROUTABLE_LINK_LOCAL_RANGE = '169.254.0.0/16';
    const UNROUTABLE_PRIVATE_USE_NETWORKS_172_RANGE = '172.16.0.0/12';
    const UNROUTABLE_IETF_PROTOCOL_ASSIGNMENTS_RANGE = '192.0.0.0/24';
    const UNROUTABLE_TEST_NET_1_RANGE = '192.0.2.0/24';
    const UNROUTABLE_6_TO_4_ANYCAST_RANGE = '192.88.99.0/24';
    const UNROUTABLE_PRIVATE_USE_NETWORKS_192_RANGE = '192.168.0.0/16';
    const UNROUTABLE_BENCHMARK_TESTING_RANGE = '198.18.0.0/15';
    const UNROUTABLE_TEST_NET_2_RANGE = '198.51.100.0/24';
    const UNROUTABLE_TEST_NET_3_RANGE = '203.0.113.0/24';
    const UNROUTABLE_MULTICAST_RANGE = '224.0.0.0/4';
    const UNROUTABLE_FUTURE_USE_RANGE = '240.0.0.0/4';
    const UNROUTABLE_LIMITED_BROADCAST_RANGE = '255.255.255.255/32';

    private $unrouteableRanges = [
        self::UNROUTABLE_THIS_NETWORK_RANGE,
        self::UNROUTABLE_PRIVATE_USE_NETWORKS_10_RANGE,
        self::UNROUTABLE_LOOPBACK_RANGE,
        self::UNROUTABLE_LINK_LOCAL_RANGE,
        self::UNROUTABLE_PRIVATE_USE_NETWORKS_172_RANGE,
        self::UNROUTABLE_IETF_PROTOCOL_ASSIGNMENTS_RANGE,
        self::UNROUTABLE_TEST_NET_1_RANGE,
        self::UNROUTABLE_6_TO_4_ANYCAST_RANGE,
        self::UNROUTABLE_PRIVATE_USE_NETWORKS_192_RANGE,
        self::UNROUTABLE_BENCHMARK_TESTING_RANGE,
        self::UNROUTABLE_TEST_NET_2_RANGE,
        self::UNROUTABLE_TEST_NET_3_RANGE,
        self::UNROUTABLE_MULTICAST_RANGE,
        self::UNROUTABLE_FUTURE_USE_RANGE,
        self::UNROUTABLE_LIMITED_BROADCAST_RANGE
    ];

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var array
     */
    private $parts = null;

    /**
     * @param string $host
     */
    public function __construct($host)
    {
        $this->set($host);
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function set($host)
    {
        $this->host = trim($host);
        $this->parts = explode(self::HOST_PART_SEPARATOR, $this->get());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param Host $comparator
     *
     * @return bool
     */
    public function equals(Host $comparator)
    {
        return $this->get() == $comparator->get();
    }

    /**
     * @param Host $comparator
     * @param string[] $excludeParts
     *
     * @return bool
     */
    public function isEquivalentTo(Host $comparator, array $excludeParts = [])
    {
        $thisHost = new Host(IdnaConvert::encodeString((string) $this));
        $comparatorHost = new Host(IdnaConvert::encodeString((string) $comparator));

        if (empty($excludeParts)) {
            return $thisHost->equals($comparatorHost);
        }

        $thisParts = $this->excludeParts($thisHost->getParts(), $excludeParts);
        $comparatorParts = $this->excludeParts($comparatorHost->getParts(), $excludeParts);

        return $thisParts == $comparatorParts;
    }

    /**
     * @param array $parts
     * @param array $exclusions
     *
     * @return array
     */
    private function excludeParts($parts, $exclusions)
    {
        $filteredParts = array();

        foreach ($parts as $index => $part) {
            if (!isset($exclusions[$index]) || $exclusions[$index] != $part) {
                $filteredParts[] = $part;
            }
        }

        return $filteredParts;
    }

    /**
     * @return bool
     *
     * @throws InvalidExpressionException
     */
    public function isPubliclyRoutable()
    {
        try {
            $ip = IpUtilsFactory::getAddress($this->get());

            if ($ip->isPrivate()) {
                return false;
            }

            if ($ip->isLoopback()) {
                return false;
            }

            if ($ip instanceof IPv4 && $this->isIpv4InUnroutableRange($ip)) {
                return false;
            }

            return true;
        } catch (\UnexpectedValueException $unexpectedValueException) {
            return true;
        }
    }

    /**
     * @param IPv4 $ip
     *
     * @return bool
     *
     * @throws InvalidExpressionException
     */
    private function isIpv4InUnroutableRange(IPv4 $ip)
    {
        foreach ($this->unrouteableRanges as $ipRange) {
            if ($ip->matches(new Subnet($ipRange))) {
                return true;
            }
        }

        return false;
    }
}
