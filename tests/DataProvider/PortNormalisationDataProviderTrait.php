<?php

namespace webignition\Tests\DataProvider;

trait PortNormalisationDataProviderTrait
{
    /**
     * @return array
     */
    public function portNormalisationDataProvider()
    {
        return [
            'port 80 is removed for http' => [
                'url' => 'http://example.com:80/',
                'expectedPortIsSet' => false,
                'expectedNormalisedPort' => null,
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'port 443 is removed for https' => [
                'url' => 'https://example.com:443/',
                'expectedPortIsSet' => false,
                'expectedNormalisedPort' => null,
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
            'port 8080 is not removed' => [
                'url' => 'http://example.com:8080/',
                'expectedPortIsSet' => true,
                'expectedNormalisedPort' => 8080,
                'expectedNormalisedUrl' => 'http://example.com:8080/',
            ],
        ];
    }
}
