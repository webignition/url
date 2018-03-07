<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Query;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class QueryTest extends AbstractNormalisedUrlTest
{
    /**
     * @dataProvider fooDataProvider
     *
     * @param string $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testCreate($queryString, $expectedNormalisedQueryString)
    {
        $normalisedQuery = new Query($queryString);
        $this->assertEquals($expectedNormalisedQueryString, (string)$normalisedQuery);
    }

    public function fooDataProvider()
    {
        return array_merge(
            $this->queryNormalisationDataProvider(),
            [
                'reserved characters are encoded and capitalised' => $this->createReservedCharactersQueryDataSet(),
            ]
        );
    }
}
