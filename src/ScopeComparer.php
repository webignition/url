<?php

namespace webignition\Url;

class ScopeComparer
{
    /**
     * @var string[]
     */
    private $ignoredParts = [
        UrlInterface::PART_PORT,
        UrlInterface::PART_USER,
        UrlInterface::PART_PASS,
        UrlInterface::PART_QUERY,
        UrlInterface::PART_FRAGMENT,
    ];

    /**
     * @var array
     */
    private $equivalentSchemes = [];

    /**
     * @var array
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
     * Is the given comparator url in the scope
     * of this url?
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
     * @param Url $sourceUrl
     * @param Url $comparator
     *
     * @return bool
     */
    public function isInScope(Url $sourceUrl, Url $comparator): bool
    {
        $localSource = clone $sourceUrl;
        $localComparator = clone $comparator;

        foreach ($this->ignoredParts as $partName) {
            $localSource->setPart($partName, null);
            $localComparator->setPart($partName, null);
        }

        $sourceString = (string) $localSource;
        $comparatorString = (string)$localComparator;

        if ($sourceString === $comparatorString) {
            return true;
        }

        if ($this->isSourceUrlSubstringOfComparatorUrl($sourceString, $comparatorString)) {
            return true;
        }

        if (!$this->areSchemesEquivalent($localSource, $localComparator)) {
            return false;
        }

        if (!$this->areHostsEquivalent($localSource, $localComparator)) {
            return false;
        }

        return $this->isSourcePathSubstringOfComparatorPath($localSource, $localComparator);
    }

    private function isSourceUrlSubstringOfComparatorUrl(string $source, string $comparator): bool
    {
        return strpos($comparator, $source) === 0;
    }

    private function areSchemesEquivalent(Url $source, Url $comparator): bool
    {
        return $this->areUrlPartsEquivalent(
            (string) $source->getScheme(),
            (string) $comparator->getScheme(),
            $this->equivalentSchemes
        );
    }

    private function areHostsEquivalent(Url $source, Url $comparator): bool
    {
        return $this->areUrlPartsEquivalent(
            (string) $source->getHost(),
            (string) $comparator->getHost(),
            $this->equivalentHosts
        );
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

    private function isSourcePathSubstringOfComparatorPath(Url $source, Url $comparator): bool
    {
        if (!$source->hasPath()) {
            return true;
        }

        $sourcePath = (string) $source->getPath();
        $comparatorPath = (string) $comparator->getPath();

        return strpos($comparatorPath, $sourcePath) === 0;
    }
}
