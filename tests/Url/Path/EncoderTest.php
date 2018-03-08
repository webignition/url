<?php

namespace webignition\Tests\Url\Path;

use webignition\Url\Path\Encoder;
use webignition\Url\Path\Path;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider encodeDataProvider
     *
     * @param Path $path
     * @param string $expectedEncodedPath
     */
    public function testEncode(Path $path, $expectedEncodedPath)
    {
        $encodedPath = Encoder::encode($path);

        $this->assertEquals($expectedEncodedPath, (string)$encodedPath);
    }

    /**
     * @return array
     */
    public function encodeDataProvider()
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
