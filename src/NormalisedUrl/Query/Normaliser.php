<?php

namespace webignition\NormalisedUrl\Query;

use webignition\Url\Query\Parser;

class Normaliser extends Parser
{
    protected function parse()
    {
        parent::parse();
        ksort($this->keyValuePairs);
    }
}
