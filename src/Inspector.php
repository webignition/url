<?php

namespace webignition\Url;

use IpUtils\Exception\InvalidExpressionException;
use webignition\Url\Host\Host;

class Inspector
{
    /**
     * @param UrlInterface $url
     *
     * @return bool
     *
     * @throws InvalidExpressionException
     */
    public function isPubliclyRoutable(UrlInterface $url): bool
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
