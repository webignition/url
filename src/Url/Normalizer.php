<?php

namespace webignition\Url;

class Normalizer
{
    public function normalize(UrlInterface $url, array $options): UrlInterface
    {
        $optionsObject = new NormalizerOptions($options);

        $normalizedUrl = clone $url;

        $this->normalizeScheme($normalizedUrl, $optionsObject);

        return $normalizedUrl;
    }

    private function normalizeScheme(UrlInterface $url, NormalizerOptions $options)
    {
        if (!$url->hasScheme() && $options->getNormalizeScheme()) {
            $url->setScheme($options->getDefaultScheme());
        }

        $url->setScheme(strtolower($url->getScheme()));
    }
}
