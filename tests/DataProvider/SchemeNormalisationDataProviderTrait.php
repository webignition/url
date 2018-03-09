<?php

namespace webignition\Tests\DataProvider;

trait SchemeNormalisationDataProviderTrait
{
    /**
     * @return array
     */
    public function schemeNormalisationDataProvider()
    {
        return [
            'http' => [
                'url' => 'http://example.com/',
                'expectedNormalisedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'HttP' => [
                'url' => 'HttP://example.com/',
                'expectedNormalisedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'HTTP' => [
                'url' => 'HTTP://example.com/',
                'expectedNormalisedScheme' => 'http',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'https' => [
                'url' => 'https://example.com/',
                'expectedNormalisedScheme' => 'https',
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
            'HttPS' => [
                'url' => 'HttPS://example.com/',
                'expectedNormalisedScheme' => 'https',
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
            'HTTPS' => [
                'url' => 'HTTPS://example.com/',
                'expectedNormalisedScheme' => 'https',
                'expectedNormalisedUrl' => 'https://example.com/',
            ],
        ];
    }
}
