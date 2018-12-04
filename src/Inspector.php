<?php

namespace webignition\Url;

use IpUtils\Exception\InvalidExpressionException;
use Psr\Http\Message\UriInterface;
use webignition\Url\Host\Host;

class Inspector
{
    /**
     * @param UriInterface $url
     *
     * @return bool
     *
     * @throws InvalidExpressionException
     */
    public function isPubliclyRoutable(UriInterface $url): bool
    {
        $host = $url->getHost();
        if (empty($host)) {
            return false;
        }

        $hostObject = new Host($host);

        if (!$hostObject->isPubliclyRoutable()) {
            return false;
        }

        $hostContainsDots = substr_count($host, '.');
        if (!$hostContainsDots) {
            return false;
        }

        $hostStartsWithDot = strpos($host, '.') === 0;
        if ($hostStartsWithDot) {
            return false;
        }

        $hostEndsWithDot = strpos($host, '.') === strlen($host) - 1;
        if ($hostEndsWithDot) {
            return false;
        }

        return true;
    }
}
