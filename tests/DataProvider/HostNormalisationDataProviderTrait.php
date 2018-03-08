<?php

namespace webignition\Tests\DataProvider;

trait HostNormalisationDataProviderTrait
{
    /**
     * @return array
     */
    public function hostNormalisationDataProvider()
    {
        return [
            'is lowercased' => [
                'url' => 'http://exAMPlE.com/',
                'expectedNormalisedHost' => 'example.com',
                'expectedNormalisedUrl' => 'http://example.com/',
            ],
            'punycode is unchanged' => [
                'url' => 'http://artesan.xn--a-iga.com/',
                'expectedNormalisedHost' => 'artesan.xn--a-iga.com',
                'expectedNormalisedUrl' => 'http://artesan.xn--a-iga.com/',
            ],
            'utf8 is converted to punycode' => [
                'url' => 'http://artesan.ía.com/',
                'expectedNormalisedHost' => 'artesan.xn--a-iga.com',
                'expectedNormalisedUrl' => 'http://artesan.xn--a-iga.com/',
            ],
        ];
    }
}
