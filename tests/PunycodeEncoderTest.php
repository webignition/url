<?php

namespace webignition\Url\Tests;

use webignition\Url\Path;
use webignition\Url\PunycodeEncoder;

class PunycodeEncoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PunycodeEncoder
     */
    private $punycodeEncoder;

    protected function setUp()
    {
        parent::setUp();

        $this->punycodeEncoder = new PunycodeEncoder();
    }

    /**
     * @dataProvider encodeDataProvider
     *
     * @param string $value
     * @param string $expectedValue
     */
    public function testEncode(string $value, string $expectedValue)
    {
        $this->assertSame($expectedValue, $this->punycodeEncoder->encode($value));
    }

    public function encodeDataProvider(): array
    {
        return [
            'ascii' => [
                'value' => 'foo',
                'expectedValue' => 'foo',
            ],
            'unicode' => [
                'value' => '♥',
                'expectedValue' => 'xn--g6h',
            ],
            'punycode' => [
                'value' => 'xn--g6h',
                'expectedValue' => 'xn--g6h',
            ],
        ];
    }

    /**
     * @dataProvider decodeDataProvider
     *
     * @param string $value
     * @param string $expectedValue
     */
    public function testDecode(string $value, string $expectedValue)
    {
        $this->assertSame($expectedValue, $this->punycodeEncoder->decode($value));
    }

    public function decodeDataProvider(): array
    {
        return [
            'ascii' => [
                'value' => 'foo',
                'expectedValue' => 'foo',
            ],
            'unicode' => [
                'value' => '♥',
                'expectedValue' => '♥',
            ],
            'punycode' => [
                'value' => 'xn--g6h',
                'expectedValue' => '♥',
            ],
        ];
    }
}
