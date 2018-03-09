<?php

namespace webignition\Tests\DataProvider;

use webignition\Url\Path\Path;

trait PathEncoderDataProviderTrait
{
    /**
     * @return array
     */
    public function pathEncoderDataProvider()
    {
        return [
            'empty path' => [
                'path' => new Path('/'),
                'expectedEncodedPath' => '/',
            ],
            'no encoding needed' => [
                'path' => new Path('/foo'),
                'expectedEncodedPath' => '/foo',
            ],
            'reserved characters are encoded' => [
                'path' => new Path('/foo/bar/!"'),
                'expectedEncodedPath' => '/foo/bar/%21%22',
            ],
        ];
    }
}
