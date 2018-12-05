<?php

namespace webignition\Url\Tests;

use webignition\Url\NormalizerOptions;

class NormalizerOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateWithNoOptions()
    {
        $normalizerOptions = new NormalizerOptions();

        $this->assertFalse($normalizerOptions->getAddPathTrailingSlash());
        $this->assertTrue($normalizerOptions->getConvertUnicodeToPunycode());
        $this->assertSame('http', $normalizerOptions->getDefaultScheme());
        $this->assertFalse($normalizerOptions->getForceHttp());
        $this->assertFalse($normalizerOptions->getForceHttps());
        $this->assertSame([], $normalizerOptions->getRemoveDefaultFilesPatterns());
        $this->assertFalse($normalizerOptions->getRemoveFragment());
        $this->assertFalse($normalizerOptions->getRemovePathDotSegments());
        $this->assertFalse($normalizerOptions->getRemoveUserInfo());
        $this->assertFalse($normalizerOptions->getRemoveWww());
        $this->assertFalse($normalizerOptions->getSetDefaultSchemeIfNoScheme());
        $this->assertFalse($normalizerOptions->getSortQueryParameters());
    }
}
