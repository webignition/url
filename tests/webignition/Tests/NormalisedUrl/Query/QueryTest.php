<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Query;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class QueryTest extends AbstractNormalisedUrlTest
{
    /**
     * @dataProvider queryNormalisationDataProvider
     *
     * @param string $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testCreate($queryString, $expectedNormalisedQueryString)
    {
        $normalisedQuery = new Query($queryString);
        $this->assertEquals($expectedNormalisedQueryString, (string)$normalisedQuery);
    }
}
