<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Normaliser;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class NormaliserTest extends AbstractNormalisedUrlTest
{
    /**
     * @dataProvider queryNormalisationDataProvider
     *
     * @param string $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testCreate($queryString, $expectedNormalisedQueryString)
    {
        $normaliser = new Normaliser($queryString);
        $normalisedKeyValuePairs = $normaliser->getKeyValuePairs();

        $queryStringParts = [];
        foreach ($normalisedKeyValuePairs as $key => $value) {
            $queryStringPart = $key;

            if ($value) {
                $queryStringPart .= '=' . $value;
            }

            $queryStringParts[] = $queryStringPart;
        }

        $this->assertEquals($expectedNormalisedQueryString, implode('&', $queryStringParts));
    }
}
