<?php

namespace webignition\Url\Tests\Query;

use webignition\Url\Query\Parser;

class ParserTest extends AbstractQueryTest
{
    /**
     * @dataProvider keyValuePairsDataProvider
     *
     * @param string|null $queryString
     * @param array $expectedKeyValuePairs
     */
    public function testGetKeyValuePairs(?string $queryString, array $expectedKeyValuePairs)
    {
        $parser = new Parser($queryString);

        $this->assertEquals($expectedKeyValuePairs, $parser->getKeyValuePairs());
    }
}
