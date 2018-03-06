<?php

namespace webignition\NormalisedUrl\Query;

use webignition\Url\Query\Query as RegularQuery;

class Query extends RegularQuery
{
    /**
     * {@inheritdoc}
     */
    public function __construct($encodedQueryString)
    {
        parent::__construct($encodedQueryString);

        $this->parser = new Normaliser($this->origin);
    }
}
