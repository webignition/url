<?php

/**
 * Check that Query::add() and ::remove() work as they should
 *  
 */
class QueryAddRemoveTest extends AbstractRegularUrlTest {      
    
    public function testAdd() {        
        $url = new \webignition\Url\Url('http://example.com/?key1=value&key2=value2');
        $url->getQuery()->add('key3', 'value3');
        
        $this->assertTrue($url->getQuery()->contains('key3'));
    }
    
    
    public function testRemove() {        
        $url = new \webignition\Url\Url('http://example.com/?key1=value&key2=value2&key3=value3');
        $url->getQuery()->remove('key3');
        
        $this->assertFalse($url->getQuery()->contains('key3'));
    }    
}