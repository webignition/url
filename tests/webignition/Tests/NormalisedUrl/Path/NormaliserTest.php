<?php

namespace webignition\Tests\NormalisedUrl\Path;

use webignition\NormalisedUrl\Path\Normaliser;

class NormaliserTest extends AbstractNormalisedUrlPathTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $path
     * @param string $expectedNormalisedPath
     */
    public function testCreate($path, $expectedNormalisedPath)
    {
        $normaliser = new Normaliser($path);
        $normalisedPath = $normaliser->get();

        $this->assertEquals($expectedNormalisedPath, $normalisedPath);
    }
}
