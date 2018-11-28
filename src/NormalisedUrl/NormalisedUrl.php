<?php

namespace webignition\NormalisedUrl;

use webignition\Url\ParserInterface;
use webignition\Url\Url as RegularUrl;

class NormalisedUrl extends RegularUrl
{
    protected function createParser(string $originUrl): ParserInterface
    {
        return new Normaliser($originUrl);
    }
}
