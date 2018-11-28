<?php

namespace webignition\NormalisedUrl\Query;

use webignition\Url\Query\ParserInterface;
use webignition\Url\Query\Query as RegularQuery;

class Query extends RegularQuery
{
    protected function createParser(?string $encodedQueryString): ParserInterface
    {
        return new Normaliser($encodedQueryString);
    }
}
