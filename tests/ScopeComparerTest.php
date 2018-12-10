<?php

namespace webignition\Url\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Url\ScopeComparer;
use webignition\Url\Url;

class ScopeComparerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isInScopeDataProvider
     *
     * @param UriInterface $sourceUrl
     * @param UriInterface $comparatorUrl
     * @param array $equivalentSchemeSets
     * @param array $equivalentHostSets
     * @param bool $expectedIsInScope
     */
    public function testIsInScope(
        UriInterface $sourceUrl,
        UriInterface $comparatorUrl,
        array $equivalentSchemeSets,
        array $equivalentHostSets,
        bool $expectedIsInScope
    ) {
        $scopeComparer = new ScopeComparer();

        if (!empty($equivalentSchemeSets)) {
            foreach ($equivalentSchemeSets as $equivalentSchemeSet) {
                $scopeComparer->addEquivalentSchemes($equivalentSchemeSet);
            }
        }

        if (!empty($equivalentHostSets)) {
            foreach ($equivalentHostSets as $equivalentHostSet) {
                $scopeComparer->addEquivalentHosts($equivalentHostSet);
            }
        }

        $this->assertEquals($expectedIsInScope, $scopeComparer->isInScope($sourceUrl, $comparatorUrl));
    }

    public function isInScopeDataProvider(): array
    {
        return [
            'two empty urls are in scope' => [
                'sourceUrl' => new Url(''),
                'comparatorUrl' => new Url(''),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different schemes, no equivalent schemes, not in scope' => [
                'sourceUrl' => new Url('http://example.com/'),
                'comparatorUrl' => new Url('https://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'different schemes, has equivalent schemes, is in scope' => [
                'sourceUrl' => new Url('http://example.com/'),
                'comparatorUrl' => new Url('https://example.com/'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'comparator as substring of source, is not in scope' => [
                'sourceUrl' => new Url('http://example.com/foo'),
                'comparatorUrl' => new Url('http://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'source as substring of comparator, is in scope' => [
                'sourceUrl' => new Url('http://example.com/'),
                'comparatorUrl' => new Url('http://example.com/foo'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different hosts, no equivalent hosts, not in scope' => [
                'sourceUrl' => new Url('http://example.com/'),
                'comparatorUrl' => new Url('https://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'different hosts, has equivalent hosts, is in scope' => [
                'sourceUrl' => new Url('http://www.example.com/'),
                'comparatorUrl' => new Url('http://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'equivalent schemes, equivalent hosts, identical path, is in scope' => [
                'sourceUrl' => new Url('https://www.example.com/'),
                'comparatorUrl' => new Url('http://example.com/'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'equivalent schemes, non-equivalent hosts, identical path, not in scope' => [
                'sourceUrl' => new Url('https://www.example.com/'),
                'comparatorUrl' => new Url('http://example.com/'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'equivalent schemes, equivalent hosts, source has no path, is in scope' => [
                'sourceUrl' => new Url('https://www.example.com'),
                'comparatorUrl' => new Url('http://example.com/foo'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'equivalent schemes, equivalent hosts, source path substring of comparator path, is in scope' => [
                'sourceUrl' => new Url('https://www.example.com/foo'),
                'comparatorUrl' => new Url('http://example.com/foo/bar'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'different ports; port difference is ignored' => [
                'sourceUrl' => new Url('http://example.com/'),
                'comparatorUrl' => new Url('http://example.com:8080/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different users; user difference is ignored' => [
                'sourceUrl' => new Url('http://foo:password@example.com/'),
                'comparatorUrl' => new Url('http://bar:password@example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different passwords; password difference is ignored' => [
                'sourceUrl' => new Url('http://user:foo@example.com/'),
                'comparatorUrl' => new Url('http://user:bar@example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different queries; query difference is ignored' => [
                'sourceUrl' => new Url('http://example.com/?foo=bar'),
                'comparatorUrl' => new Url('http://example.com/?bar=foo'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different fragments; fragment difference is ignored' => [
                'sourceUrl' => new Url('http://example.com/#foo'),
                'comparatorUrl' => new Url('http://example.com/#bar'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
        ];
    }
}
