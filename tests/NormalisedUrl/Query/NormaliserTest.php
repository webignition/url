<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Normaliser;
use webignition\Tests\DataProvider\QueryNormalisationDataProviderTrait;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class NormaliserTest extends AbstractNormalisedUrlTest
{
    use QueryNormalisationDataProviderTrait;

    /**
     * @dataProvider queryNormalisationDataProvider
     *
     * @param string|null $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testCreate(?string $queryString, string $expectedNormalisedQueryString)
    {
        $normaliser = new Normaliser($queryString);
        $normalisedKeyValuePairs = $normaliser->getKeyValuePairs();

        $queryStringParts = [];
        foreach ($normalisedKeyValuePairs as $key => $value) {
            $queryStringPart = $key;

            if (!is_null($value)) {
                $queryStringPart .= '=' . $value;
            }

            $queryStringParts[] = $queryStringPart;
        }

        $this->assertEquals($expectedNormalisedQueryString, implode('&', $queryStringParts));
    }
}
