<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;

use webignition\Url\Url;

class ConvertIdnToUtf8Test extends AbstractRegularUrlTest {   
    
    public function testToStringWithConvertIdnToUtf8Disabled() {
        $source = 'http://artesan.xn--a-iga.com/';
        
        $url = new Url($source);
        $this->assertEquals($source, (string)$url);
    }
    
    public function testToStringWithConvertIdnToUtf8Enabled() {
        $source = 'http://artesan.xn--a-iga.com/';
        $expectedUrl = 'http://artesan.ía.com/';
        
        $url = new Url($source);
        $url->getConfiguration()->enableConvertIdnToUtf8();
        $this->assertEquals($expectedUrl, (string)$url);
    }
}