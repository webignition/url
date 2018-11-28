<?php

namespace webignition\NormalisedUrl;

use Etechnika\IdnaConvert\IdnaConvert;
use webignition\Url\Host\Host;
use webignition\Url\Parser;
use webignition\Url\UrlInterface;

class Normaliser extends Parser
{
    const DEFAULT_PORT = 80;

    private $knownPorts = [
        'http' => 80,
        'https' => 443
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(?string $url)
    {
        parent::__construct($url);

        $this->normaliseScheme();
        $this->normaliseHost();
        $this->normalisePort();
        $this->normalisePath();
        $this->normaliseQuery();
    }

    /**
     * Scheme is case-insensitive, normalise to lowercase
     */
    private function normaliseScheme()
    {
        if (isset($this->parts[UrlInterface::PART_SCHEME])) {
            $this->parts[UrlInterface::PART_SCHEME] = strtolower(trim($this->parts[UrlInterface::PART_SCHEME]));
        }
    }

    /**
     * Host is case-insensitive, normalise to lowercase and to ascii version of
     * IDN format
     *
     * If host has trailing dots and there is no path, trim the trailing dots
     * e.g http://example.com. is interpreted as host=example.com. path=
     *     and needs to be understood as host=example.com and path=
     *
     *     http://example.com.. is interpreted as host=example.com.. path=
     *     and needs to be understood as host=example.com and path=
     */
    private function normaliseHost()
    {
        $hasHost = isset($this->parts[UrlInterface::PART_HOST]);

        if ($hasHost) {
            /* @var Host $host */
            $host = $this->parts[UrlInterface::PART_HOST];
            $hostAsString = $host->get();

            $asciiHost = strtolower(IdnaConvert::encodeString($hostAsString));

            $hostHasTrailingDots = preg_match('/\.+$/', $asciiHost) > 0;
            $hasPath = isset($this->parts[UrlInterface::PART_PATH]);

            if ($hostHasTrailingDots && !$hasPath) {
                $asciiHost = rtrim($asciiHost, '.');
            }

            $host->set($asciiHost);

            $this->parts[UrlInterface::PART_HOST] = $host;
        }
    }

    /**
     * Remove default HTTP(S) port
     */
    private function normalisePort()
    {
        $hasPort = isset($this->parts[UrlInterface::PART_PORT]);
        $hasScheme = isset($this->parts[UrlInterface::PART_SCHEME]);

        if ($hasPort && $hasScheme) {
            $port = $this->parts[UrlInterface::PART_PORT];
            $scheme = $this->parts[UrlInterface::PART_SCHEME];

            $hasKnownPort = isset($this->knownPorts[$scheme]);

            if ($hasKnownPort && $this->knownPorts[$scheme] == $port) {
                unset($this->parts[UrlInterface::PART_PORT]);
            }
        }
    }

    private function normalisePath()
    {
        if (!isset($this->parts[UrlInterface::PART_PATH])) {
            $this->parts[UrlInterface::PART_PATH] = null;
        }

        $this->parts[UrlInterface::PART_PATH] = new Path\Path((string)$this->parts[UrlInterface::PART_PATH]);
    }

    private function normaliseQuery()
    {
        $this->parts[UrlInterface::PART_QUERY] = new Query\Query((string)$this->parts[UrlInterface::PART_QUERY]);
    }
}
