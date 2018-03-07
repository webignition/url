<?php

namespace webignition\Tests\NormalisedUrl\Path;

use webignition\NormalisedUrl\Path\Normaliser;
use webignition\Tests\NormalisedUrl\AbstractNormalisedUrlTest;

class NormaliserTest extends AbstractNormalisedUrlTest
{
    /**
     * @dataProvider pathNormalisationDataProvider
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
