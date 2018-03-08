<?php

namespace webignition\Url;

class ScopeComparer
{
    /**
     * @var Url
     */
    private $sourceUrl;

    /**
     * @var Url
     */
    private $comparatorUrl;

    /**
     * @var string
     */
    private $sourceUrlString;

    /**
     * @var string
     */
    private $comparatorUrlString;

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
    public function addEquivalentSchemes($schemes)
    {
        $this->equivalentSchemes[] = $schemes;
    }

    /**
     * @param string[] $hosts
     */
    public function addEquivalentHosts($hosts)
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
     * @param Url $comparatorUrl
     *
     * @return bool
     */
    public function isInScope(Url $sourceUrl, Url $comparatorUrl)
    {
        $this->sourceUrl = clone $sourceUrl;
        $this->comparatorUrl = clone $comparatorUrl;

        foreach ($this->ignoredParts as $partName) {
            $this->sourceUrl->setPart($partName, null);
        }

        $this->sourceUrlString = (string)$this->sourceUrl;
        $this->comparatorUrlString = (string)$this->comparatorUrl;

        if ($this->sourceUrlString === $this->comparatorUrlString) {
            return true;
        }

        if ($this->isSourceUrlSubstringOfComparatorUrl()) {
            return true;
        }

        if (!$this->areSchemesEquivalent()) {
            return false;
        }

        if (!$this->areHostsEquivalent()) {
            return false;
        }

        return $this->isSourcePathSubstringOfComparatorPath();
    }

    /**
     * @return bool
     */
    private function isSourceUrlSubstringOfComparatorUrl()
    {
        return strpos($this->comparatorUrlString, $this->sourceUrlString) === 0;
    }

    /**
     * @return bool
     */
    private function areSchemesEquivalent()
    {
        return $this->areUrlPartsEquivalent(
            (string)$this->sourceUrl->getScheme(),
            (string)$this->comparatorUrl->getScheme(),
            $this->equivalentSchemes
        );
    }

    /**
     * @return bool
     */
    private function areHostsEquivalent()
    {
        return $this->areUrlPartsEquivalent(
            (string)$this->sourceUrl->getHost(),
            (string)$this->comparatorUrl->getHost(),
            $this->equivalentHosts
        );
    }

    /**
     * @param string $sourceValue
     * @param string $comparatorValue
     * @param array $equivalenceSets
     *
     * @return bool
     */
    private function areUrlPartsEquivalent($sourceValue, $comparatorValue, $equivalenceSets)
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

    /**
     * @return bool
     */
    private function isSourcePathSubstringOfComparatorPath()
    {
        if (!$this->sourceUrl->hasPath()) {
            return true;
        }

        $sourcePath = (string)$this->sourceUrl->getPath();
        $comparatorPath = (string)$this->comparatorUrl->getPath();

        return strpos($comparatorPath, $sourcePath) === 0;
    }
}
