<?php

namespace webignition\Tests\NormalisedUrl;

use webignition\NormalisedUrl\Normaliser;
use webignition\Url\UrlInterface;

class NormaliserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider normaliseSchemeDataProvider
     *
     * @param string $url
     * @param string $expectedNormalisedScheme
     */
    public function testNormalisedScheme($url, $expectedNormalisedScheme)
    {
        $normaliser = new Normaliser($url);

        $normalisedParts = $normaliser->getParts();

        $this->assertEquals($expectedNormalisedScheme, $normalisedParts[UrlInterface::PART_SCHEME]);
    }

    /**
     * @return array
     */
    public function normaliseSchemeDataProvider()
    {
        return [
            'http' => [
                'url' => 'http://example.com/',
                'expectedNormalisedScheme' => 'http',
            ],
            'HttP' => [
                'url' => 'HttP://example.com/',
                'expectedNormalisedScheme' => 'http',
            ],
            'HTTP' => [
                'url' => 'HTTP://example.com/',
                'expectedNormalisedScheme' => 'http',
            ],
            'https' => [
                'url' => 'https://example.com/',
                'expectedNormalisedScheme' => 'https',
            ],
            'HttPS' => [
                'url' => 'HttPS://example.com/',
                'expectedNormalisedScheme' => 'https',
            ],
            'HTTPS' => [
                'url' => 'HTTPS://example.com/',
                'expectedNormalisedScheme' => 'https',
            ],
        ];
    }

}
