<?php

namespace webignition\Tests\NormalisedUrl\Path;

use webignition\NormalisedUrl\Path\Path;
use webignition\Tests\DataProvider\PathNormalisationDataProviderTrait;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class PathTest extends AbstractNormalisedUrlTest
{
    use PathNormalisationDataProviderTrait;

    /**
     * @dataProvider pathNormalisationDataProvider
     *
     * @param string $path
     * @param string $expectedNormalisedPath
     */
    public function testCreate($path, $expectedNormalisedPath)
    {
        $normalisedPath = new Path($path);

        $this->assertEquals($expectedNormalisedPath, $normalisedPath);
    }
}
