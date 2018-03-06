<?php

namespace webignition\Tests\NormalisedUrl\Path;

use webignition\NormalisedUrl\Path\Path;

class PathTest extends AbstractNormalisedUrlPathTest
{
    /**
     * @dataProvider createDataProvider
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
