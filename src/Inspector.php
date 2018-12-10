<?php

namespace webignition\Url;

use IpUtils\Exception\InvalidExpressionException;
use Psr\Http\Message\UriInterface;

class Inspector
{
    /**
     * Can a URL fail to be accessed over the public internet?
     *
     * There are certain cases where we can be certain that a URL cannot be accessed over the public internet:
     *
     * - no host
     * - having a host that is not publicly routable (such as a within a private IP range)
     * - having a hostname lacking dots
     * - having a hostname starting or ending with a dot
     *
     * @param UriInterface $url
     *
     * @return bool
     *
     * @throws InvalidExpressionException
     */
    public static function isNotPubliclyRoutable(UriInterface $url): bool
    {
        $host = $url->getHost();
        if ('' === $host) {
            return true;
        }

        $hostObject = new Host($host);

        if (!$hostObject->isPubliclyRoutable()) {
            return true;
        }

        $hostContainsDots = substr_count($host, '.');
        if (!$hostContainsDots) {
            return true;
        }

        if ('.' === $host[0] || '.' === $host[-1]) {
            return true;
        }

        return false;
    }

    public static function isProtocolRelative(UriInterface $uri): bool
    {
        $scheme = $uri->getScheme();

        if ('' !== $scheme) {
            return false;
        }

        return '' !== $uri->getHost();
    }
}
