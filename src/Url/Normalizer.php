<?php

namespace webignition\Url;

class Normalizer
{
    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    public function __construct()
    {
        $this->punycodeEncoder = new PunycodeEncoder();
    }

    public function normalize(UrlInterface $url, array $options): UrlInterface
    {
        $optionsObject = new NormalizerOptions($options);

        $normalizedUrl = clone $url;

        $this->normalizeScheme($normalizedUrl, $optionsObject);

        if ($optionsObject->getForceHttp()) {
            $normalizedUrl->setScheme(NormalizerOptions::SCHEME_HTTP);
        }

        if ($optionsObject->getForceHttps()) {
            $normalizedUrl->setScheme(NormalizerOptions::SCHEME_HTTPS);
        }

        if ($optionsObject->getRemoveUserInfo()) {
            $normalizedUrl->setUser(null);
            $normalizedUrl->setPass(null);
        }

        if ($optionsObject->getRemoveFragment()) {
            $normalizedUrl->setFragment(null);
        }

        if ($normalizedUrl->hasHost()) {
            $this->normalizeHost($normalizedUrl, $optionsObject);
        }

        return $normalizedUrl;
    }

    private function normalizeScheme(UrlInterface $url, NormalizerOptions $options)
    {
        if (!$url->hasScheme() && $options->getSetDefaultSchemeIfNoScheme()) {
            $url->setScheme($options->getDefaultScheme());
        }

        $url->setScheme(strtolower($url->getScheme()));
    }

    /**
     * Host normalization
     * - convert to lowercase
     * - ascii version of IDN format
     * - trailing dot removal
     *
     * If host has trailing dots and there is no path, trim the trailing dots
     * e.g http://example.com. is interpreted as host=example.com. path=
     *     and needs to be understood as host=example.com and path=
     *
     *     http://example.com.. is interpreted as host=example.com.. path=
     *     and needs to be understood as host=example.com and path=
     *
     * @param UrlInterface $url
     * @param NormalizerOptions $options
     */
    private function normalizeHost(UrlInterface $url, NormalizerOptions $options)
    {
        $hostObject = $url->getHost();

        $host = (string) $hostObject;

        if ($options->getConvertUnicodeToPunycode()) {
            $host = $this->punycodeEncoder->encode($host);
        }

        $host = strtolower($host);

        $hostHasTrailingDots = preg_match('/\.+$/', $host) > 0;
        if ($hostHasTrailingDots) {
            $host = rtrim($host, '.');
        }

        $url->setHost($host);
    }
}
