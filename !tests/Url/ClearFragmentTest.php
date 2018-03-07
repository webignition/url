<?php

namespace webignition\Tests\Url;
use webignition\Url\Url;

/**
 * Check that URL properties can be set
 *  
 */
class ClearFragmentTest extends AbstractRegularUrlTest {
    
    public function testClearWhenFragmentIsEmtpy() {
        $url = new Url('http://example.com#');
        $url->setFragment(null);

        $this->assertEquals('http://example.com', (string)$url);
    }


    public function testClearWhenFragmentIsNotEmtpy() {
        $url = new Url('http://example.com#foo');
        $url->setFragment(null);

        $this->assertEquals('http://example.com', (string)$url);
    }
}