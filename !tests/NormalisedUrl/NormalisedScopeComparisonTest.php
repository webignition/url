<?php

namespace webignition\Tests\NormalisedUrl;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class NormalisedScopeComparisonTest extends AbstractNormalisedUrlTest {

    public function testCompareWithSelf() {
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $this->assertTrue($this->getComparer()->isInScope($url, $url));
    }

    public function testCompareWithReferenceToSelf() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = $url1;

        $this->assertTrue($this->getComparer()->isInScope($url1, $url2));
    }

    public function testPathOneLevel() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/one/');

        $this->assertTrue($this->getComparer()->isInScope($url1, $url2));
    }

    public function testPathTwoLevels() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/one/two/');

        $this->assertTrue($this->getComparer()->isInScope($url1, $url2));
    }

    public function testSchemeEquivalence() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('https://example.com/');

        $this->assertTrue($this->getComparer()->isInScope($url1, $url2));
    }

    public function testHostEquivalence() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('http://www.example.com/');

        $comparer = $this->getComparer();
        $comparer->addEquivalentHosts(array(
            'example.com',
            'www.example.com'
        ));

        $this->assertTrue($comparer->isInScope($url1, $url2));
    }

    public function testSchemeAndHostEquivalence() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('https://www.example.com/');

        $comparer = $this->getComparer();
        $comparer->addEquivalentHosts(array(
            'example.com',
            'www.example.com'
        ));

        $this->assertTrue($comparer->isInScope($url1, $url2));
    }

    public function testSchemeEquivalenceAndPathOneLevel() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('https://example.com/one/');

        $this->assertTrue($this->getComparer()->isInScope($url1, $url2));
    }

    public function testSchemeEquivalenceAndPathTwoLevel() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('https://example.com/one/two/');

        $this->assertTrue($this->getComparer()->isInScope($url1, $url2));
    }

    public function testHostEquivalenceAndPathOneLevel() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('http://www.example.com/one/');

        $comparer = $this->getComparer();
        $comparer->addEquivalentHosts(array(
            'example.com',
            'www.example.com'
        ));

        $this->assertTrue($comparer->isInScope($url1, $url2));
    }

    public function testHostEquivalenceAndPathTwoLevels() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('http://www.example.com/one/two/');

        $comparer = $this->getComparer();
        $comparer->addEquivalentHosts(array(
            'example.com',
            'www.example.com'
        ));

        $this->assertTrue($comparer->isInScope($url1, $url2));
    }

    public function testSchemeAndHostEquivalenceAndPathOneLevel() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('https://www.example.com/one/');

        $comparer = $this->getComparer();
        $comparer->addEquivalentHosts(array(
            'example.com',
            'www.example.com'
        ));

        $this->assertTrue($comparer->isInScope($url1, $url2));
    }

    public function testSchemeAndHostEquivalenceAndPathTwoLevels() {
        $url1 = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url2 = new \webignition\NormalisedUrl\NormalisedUrl('https://www.example.com/one/two/');

        $comparer = $this->getComparer();
        $comparer->addEquivalentHosts(array(
            'example.com',
            'www.example.com'
        ));

        $this->assertTrue($comparer->isInScope($url1, $url2));
    }


    /**
     *
     * @return \webignition\Url\ScopeComparer
     */
    private function getComparer() {
        $comparer = new \webignition\Url\ScopeComparer();
        $comparer->addEquivalentSchemes(array(
            'http',
            'https'
        ));

        return $comparer;
    }
}