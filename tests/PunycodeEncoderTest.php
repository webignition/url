<?php

namespace webignition\Url\Tests;

use webignition\Url\PunycodeEncoder;

class PunycodeEncoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider encodeDataProvider
     *
     * @param string $value
     * @param string $expectedValue
     */
    public function testEncode(string $value, string $expectedValue)
    {
        $this->assertSame($expectedValue, PunycodeEncoder::encode($value));
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
        $this->assertSame($expectedValue, PunycodeEncoder::decode($value));
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
