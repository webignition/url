<?php

/**
  0.0.0.0/8           "This" Network             RFC 1122, Section 3.2.1.3
  10.0.0.0/8          Private-Use Networks       RFC 1918
  127.0.0.0/8         Loopback                   RFC 1122, Section 3.2.1.3
  169.254.0.0/16      Link Local                 RFC 3927
  172.16.0.0/12       Private-Use Networks       RFC 1918
  192.0.0.0/24        IETF Protocol Assignments  RFC 5736
  192.0.2.0/24        TEST-NET-1                 RFC 5737
  192.88.99.0/24      6to4 Relay Anycast         RFC 3068
  192.168.0.0/16      Private-Use Networks       RFC 1918
  198.18.0.0/15       Network Interconnect Device Benchmark Testing   RFC 2544
  198.51.100.0/24     TEST-NET-2                 RFC 5737
  203.0.113.0/24      TEST-NET-3                 RFC 5737
  224.0.0.0/4         Multicast                  RFC 3171
  240.0.0.0/4         Reserved for Future Use    RFC 1112, Section 4
  255.255.255.255/32  Limited Broadcast          RFC 919, Section 7 RFC 922, Section 7
 */
class IsPubliclyRoutableTest extends AbstractRegularUrlTest {
    
    public function testIsRoutableForDomain() {
        $host = new webignition\Url\Host\Host('example.com');
        $this->assertTrue($host->isPubliclyRoutable());
    }

    public function testIsNotRoutableFor0_0_0_0Slash8() {        
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }    
    
    public function testIsRoutableFor1_0_0_0Slash8() {        
        $this->isRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }     
    
    public function testIsRoutableFor2_0_0_0Slash8() {        
        $this->isRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }     
    
    public function testIsNotRoutableFor10_0_0_0Slash8() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor127_0_0_0Slash8() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor169_254_0_0Slash16() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }
    
    public function testIsNotRoutableFor172_16_0_0Slash12() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor192_0_0_0Slash24() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor192_0_2_0Slash24() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor192_88_99_0Slash24() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor192_168_0_0Slash16() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor198_18_0_0Slash15() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor198_51_100_0Slash24() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor203_0_113_0Slash24() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }

    public function testIsNotRoutableFor224_0_0_0Slash4() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }    
    
    public function testIsNotRoutableFor240_0_0_0Slash4() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }    
    
    public function testIsNotRoutableFor255_255_255Slash32() {
        $this->isNotRoutableForIpRangeTest($this->getIpRangeFromFunctionName(__FUNCTION__));
    }       
    
    
    private function getIpRangeFromFunctionName($functionName) {
        $forSplit = explode('For', $functionName);
        $slashSplit = explode('Slash', $forSplit[1]);
        
        return str_replace('_', '.', $slashSplit[0]) . '/' . $slashSplit[1];
    }
    
    private function isRoutableForIpRangeTest($range) {
        $this->isRoutableRangeTest($range, true);       
    }    
    
    private function isNotRoutableForIpRangeTest($range) {
        $this->isRoutableRangeTest($range, false);
    }
    
    
    private function isRoutableRangeTest($range, $expectedBooleanResult) {
        $ipRangeSplit = explode('/', $range);
        
        $startIp = $ipRangeSplit[0];
        $cidrRange = (int)$ipRangeSplit[1];
        $ipCount = 1 << (32 - $cidrRange);

        $first = ip2long($startIp);        
        $last = $first + $ipCount - 1;
        
        $ipsToTest = array_merge(
            array($first, $last),
            $this->getRandomLongIpSubsetInRange($first, $last)
        );
        
        foreach ($ipsToTest as $longIp) {
            $host = new webignition\Url\Host\Host(long2ip($longIp));
            
            if ($expectedBooleanResult) {
                $this->assertTrue($host->isPubliclyRoutable());            
            } else {
                $this->assertFalse($host->isPubliclyRoutable());
            }            
        }           
    }
    
    
    /**
     * 
     * @param long $first
     * @param long $last
     * @return array
     */
    private function getRandomLongIpSubsetInRange($first, $last) {
        $total = 32;
        $ips = array();
        
        while (count($ips) < $total) {
            $ips[] = rand($first, $last);
        }
        
        return $ips;
    }



}