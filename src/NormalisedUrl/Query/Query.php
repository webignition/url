<?php

namespace webignition\NormalisedUrl\Query;

use webignition\Url\Query\Query as RegularQuery;

class Query extends RegularQuery
{
    /**
     * {@inheritdoc}
     */
    protected function createParser($encodedQueryString)
    {
        return new Normaliser($encodedQueryString);
    }
}
