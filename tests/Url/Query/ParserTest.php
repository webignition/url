<?php

namespace webignition\Tests\Url\Query;

use webignition\Url\Query\Parser;

class ParserTest extends AbstractQueryTest
{
    /**
     * @dataProvider keyValuePairsDataProvider
     *
     * @param string $queryString
     * @param array $expectedKeyValuePairs
     */
    public function testGetKeyValuePairs($queryString, array $expectedKeyValuePairs)
    {
        $parser = new Parser($queryString);

        $this->assertEquals($expectedKeyValuePairs, $parser->getKeyValuePairs());
    }
}
