<?php

namespace webignition\Tests\NormalisedUrl;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

use webignition\NormalisedUrl\NormalisedUrl;

class ConvertIdnToUtf8Test extends AbstractNormalisedUrlTest {   
    
    public function testToStringWithConvertIdnToUtf8Disabled() {
        $source = 'http://artesan.xn--a-iga.com/';
        
        $url = new NormalisedUrl($source);
        $this->assertEquals($source, (string)$url);
    }
    
    public function testToStringWithConvertIdnToUtf8Enabled() {
        $source = 'http://artesan.xn--a-iga.com/';
        $expectedUrl = 'http://artesan.ía.com/';
        
        $url = new NormalisedUrl($source);
        $url->getConfiguration()->enableConvertIdnToUtf8();
        $this->assertEquals($expectedUrl, (string)$url);
    }
}