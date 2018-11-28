<?php

namespace webignition\Url;

class Normalizer
{
    public function normalize(UrlInterface $url): UrlInterface
    {
        $normalizedUrl = clone $url;

        return $normalizedUrl;
    }
}
