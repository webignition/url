<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;

class ParseCredentialsTest extends AbstractRegularUrlTest {
    
    public function testHasSchemeEmptyUsernameNoPassword() {
        $url = new \webignition\Url\Url('https://@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('', $url->getUser());

        $this->assertFalse($url->hasPass());
        $this->assertNull($url->getPass());
    }

    public function testProtocolRelativeEmptyUsernameNoPassword() {
        $url = new \webignition\Url\Url('//@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('', $url->getUser());

        $this->assertFalse($url->hasPass());
        $this->assertNull($url->getPass());
    }


    public function testEmptyUsernameEmptyPassword() {
        $url = new \webignition\Url\Url('https://:@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('', $url->getUser());

        $this->assertTrue($url->hasPass());
        $this->assertEquals('', $url->getPass());
    }

    public function testProtocolRelativeEmptyUsernameEmptyPassword() {
        $url = new \webignition\Url\Url('//:@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('', $url->getUser());

        $this->assertTrue($url->hasPass());
        $this->assertEquals('', $url->getPass());
    }

    public function testEmptyUsernameHasPassword() {
        $url = new \webignition\Url\Url('https://:password@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('', $url->getUser());

        $this->assertTrue($url->hasPass());
        $this->assertEquals('password', $url->getPass());
    }

    public function testProtocolRelativeEmptyUsernameHasPassword() {
        $url = new \webignition\Url\Url('//:password@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('', $url->getUser());

        $this->assertTrue($url->hasPass());
        $this->assertEquals('password', $url->getPass());
    }

    public function testHasUsernameEmptyPassword() {
        $url = new \webignition\Url\Url('https://username:@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('username', $url->getUser());

        $this->assertTrue($url->hasPass());
        $this->assertEquals('', $url->getPass());
    }

    public function testProtocolRelativeHasUsernameEmptyPassword() {
        $url = new \webignition\Url\Url('//username:@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('username', $url->getUser());

        $this->assertTrue($url->hasPass());
        $this->assertEquals('', $url->getPass());
    }


    public function testHasUsernameNoPassword() {
        $url = new \webignition\Url\Url('https://username@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('username', $url->getUser());

        $this->assertFalse($url->hasPass());
    }


    public function testProtocolRelativeHasUsernameNoPassword() {
        $url = new \webignition\Url\Url('//username@example.com');

        $this->assertTrue($url->hasUser());
        $this->assertEquals('username', $url->getUser());

        $this->assertFalse($url->hasPass());
    }
}