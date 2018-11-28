<?php

namespace webignition\Tests\NormalisedUrl\Query;

use webignition\NormalisedUrl\Query\Query;
use webignition\Tests\DataProvider\QueryNormalisationDataProviderTrait;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class QueryTest extends AbstractNormalisedUrlTest
{
    use QueryNormalisationDataProviderTrait;

    /**
     * @dataProvider createDataProvider
     *
     * @param string|null $queryString
     * @param string $expectedNormalisedQueryString
     */
    public function testCreate(?string $queryString, string $expectedNormalisedQueryString)
    {
        $normalisedQuery = new Query($queryString);
        $this->assertEquals($expectedNormalisedQueryString, (string)$normalisedQuery);
    }

    public function createDataProvider(): array
    {
        return array_merge(
            $this->queryNormalisationDataProvider(),
            [
                'reserved characters are encoded and capitalised' => $this->createReservedCharactersQueryDataSet(),
            ]
        );
    }
}
