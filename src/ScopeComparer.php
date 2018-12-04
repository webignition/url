<?php

namespace webignition\Url;

use Psr\Http\Message\UriInterface;

class ScopeComparer
{
    /**
     * @var string[]
     */
    private $equivalentSchemes = [];

    /**
     * @var string[]
     */
    private $equivalentHosts = [];

    /**
     * @param string[] $schemes
     */
    public function addEquivalentSchemes(array $schemes)
    {
        $this->equivalentSchemes[] = $schemes;
    }

    /**
     * @param string[] $hosts
     */
    public function addEquivalentHosts(array $hosts)
    {
        $this->equivalentHosts[] = $hosts;
    }

    /**
     * Is the given comparator url in the scope of the source url?
     *
     * Comparator is in the same scope as the source if:
     *  - scheme is the same or equivalent (e.g. http and https are equivalent)
     *  - hostname is the same or equivalent (equivalency looks at subdomain equivalence
     *    e.g. example.com and www.example.com)
     *  - path is the same or greater (e.g. sourcepath = /one/two, comparatorpath = /one/two or /one/two/*
     *
     * Comparison ignores:
     *  - port
     *  - user
     *  - pass
     *  - query
     *  - fragment
     *
     * @param UriInterface $source
     * @param UriInterface $comparator
     *
     * @return bool
     */
    public function isInScope(UriInterface $source, UriInterface $comparator): bool
    {
        $source = $this->removeIgnoredComponents($source);
        $comparator = $this->removeIgnoredComponents($comparator);

        $sourceString = (string) $source;
        $comparatorString = (string) $comparator;

        if ($sourceString === $comparatorString) {
            return true;
        }

        if ($this->isSourceUrlSubstringOfComparatorUrl($sourceString, $comparatorString)) {
            return true;
        }

        if (!$this->areSchemesEquivalent($source->getScheme(), $comparator->getScheme())) {
            return false;
        }

        if (!$this->areHostsEquivalent($source->getHost(), $comparator->getHost())) {
            return false;
        }

        return $this->isSourcePathSubstringOfComparatorPath($source->getPath(), $comparator->getPath());
    }

    private function isSourceUrlSubstringOfComparatorUrl(string $source, string $comparator): bool
    {
        return strpos($comparator, $source) === 0;
    }

    private function areSchemesEquivalent(string $source, string $comparator): bool
    {
        return $this->areUrlPartsEquivalent($source, $comparator, $this->equivalentSchemes);
    }

    private function areHostsEquivalent(string $source, string $comparator): bool
    {
        return $this->areUrlPartsEquivalent($source, $comparator, $this->equivalentHosts);
    }

    private function areUrlPartsEquivalent(string $sourceValue, string $comparatorValue, array $equivalenceSets): bool
    {
        if ($sourceValue === $comparatorValue) {
            return true;
        }

        foreach ($equivalenceSets as $equivalenceSet) {
            if (in_array($sourceValue, $equivalenceSet) && in_array($comparatorValue, $equivalenceSet)) {
                return true;
            }
        }

        return false;
    }

    private function isSourcePathSubstringOfComparatorPath(string $source, string $comparator): bool
    {
        if ('' === $source) {
            return true;
        }

        return 0 === strpos($comparator, $source);
    }

    private function removeIgnoredComponents(UriInterface $uri): UriInterface
    {
        $uri = $uri->withPort(null);
        $uri = $uri->withUserInfo('');
        $uri = $uri->withQuery('');
        $uri = $uri->withFragment('');

        return $uri;
    }
}
