<?php

namespace webignition\Tests\Url\Path;

use webignition\Tests\DataProvider\PathEncoderDataProviderTrait;
use webignition\Url\Path\Encoder;
use webignition\Url\Path\Path;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    use PathEncoderDataProviderTrait;

    /**
     * @dataProvider pathEncoderDataProvider
     *
     * @param Path $path
     * @param string $expectedEncodedPath
     */
    public function testEncode(Path $path, $expectedEncodedPath)
    {
        $encodedPath = Encoder::encode($path);

        $this->assertEquals($expectedEncodedPath, (string)$encodedPath);
    }
}
