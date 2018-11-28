<?php

namespace webignition\Tests\DataProvider;

trait PathNormalisationDataProviderTrait
{
    public function pathNormalisationDataProvider(): array
    {
        return [
            'null path' => [
                'path' => null,
                'expectedNormalisedPath' => '/',
            ],
            'empty path' => [
                'path' => '',
                'expectedNormalisedPath' => '/',
            ],
            'slash' => [
                'path' => '/',
                'expectedNormalisedPath' => '/',
            ],
            'single dot' => [
                'path' => '.',
                'expectedNormalisedPath' => '/',
            ],
            'slash single dot' => [
                'path' => '/.',
                'expectedNormalisedPath' => '/',
            ],
            'double dot' => [
                'path' => '..',
                'expectedNormalisedPath' => '/',
            ],
            'slash double dot' => [
                'path' => '/..',
                'expectedNormalisedPath' => '/',
            ],
            'rfc3986 5.2.4 example 1' => [
                'path' => '/a/b/c/./../../g',
                'expectedNormalisedPath' => '/a/g',
            ],
            'rfc3986 5.2.4 example 2' => [
                'path' => '/mid/content=5/../6',
                'expectedNormalisedPath' => '/mid/6',
            ],
            'many single dot' => [
                'path' => '/./././././././././././././././',
                'expectedNormalisedPath' => '/',
            ],
            'many double dot' => [
                'path' => '/../../../../../../',
                'expectedNormalisedPath' => '/',
            ],
            'double trailing slash' => [
                'path' => '//',
                'expectedNormalisedPath' => '/',
            ],
            'triple trailing slash' => [
                'path' => '///',
                'expectedNormalisedPath' => '/',
            ],
            'non-empty path with double trailing slash' => [
                'path' => '/one/two//',
                'expectedNormalisedPath' => '/one/two/',
            ],
            'non-empty path with triple trailing slash' => [
                'path' => '/one/two///',
                'expectedNormalisedPath' => '/one/two/',
            ],
            'non-empty path with leading double shash mid double slash and trailing double slash' => [
                'path' => '//one//two//',
                'expectedNormalisedPath' => '//one//two/',
            ],
            'non-empty path with leading triple slash mid triple slash and trailing triple slash' => [
                'path' => '///one///two///',
                'expectedNormalisedPath' => '///one///two/',
            ],
            'non-empty path with double mid slash and no trailing slash' => [
                'path' => '/one//two',
                'expectedNormalisedPath' => '/one//two',
            ],
            'percent-encode unicode characters' => [
                'path' => '/Nattō',
                'expectedNormalisedPath' => '/Natt%C5%8D',
            ],
        ];
    }
}
