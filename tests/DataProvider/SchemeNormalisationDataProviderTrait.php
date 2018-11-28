<?php

namespace webignition\Tests\DataProvider;

trait SchemeNormalisationDataProviderTrait
{
    public function schemeNormalisationDataProvider(): array
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
