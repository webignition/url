<?php

namespace webignition\Tests\Url;

use webignition\Url\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->configuration = new Configuration();
    }

    /**
     * @dataProvider configurationDataProvider
     *
     * @param bool|null $fullyEncodeQueryStringKeys
     * @param bool|null $convertIdnToUtf8
     * @param bool $expectedFullyEncodeQueryStringKeys
     * @param bool $expectedConvertIdnToUtf8
     */
    public function testConfiguration(
        ?bool $fullyEncodeQueryStringKeys,
        ?bool $convertIdnToUtf8,
        bool $expectedFullyEncodeQueryStringKeys,
        bool $expectedConvertIdnToUtf8
    ) {
        if (!is_null($fullyEncodeQueryStringKeys)) {
            if ($fullyEncodeQueryStringKeys) {
                $this->configuration->enableFullyEncodeQueryStringKeys();
            } else {
                $this->configuration->disableFullyEncodeQueryStringKeys();
            }
        }

        if (!is_null($convertIdnToUtf8)) {
            if ($convertIdnToUtf8) {
                $this->configuration->enableConvertIdnToUtf8();
            } else {
                $this->configuration->disableConvertIdnToUtf8();
            }
        }

        $this->assertEquals($expectedFullyEncodeQueryStringKeys, $this->configuration->getFullyEncodeQueryStringKeys());
        $this->assertEquals($expectedConvertIdnToUtf8, $this->configuration->getConvertIdnToUtf8());
    }

    public function configurationDataProvider(): array
    {
        return [
            'default' => [
                'fullyEncodeQueryStringKeys' => null,
                'convertIdnToUtf8' => null,
                'expectedFullyEncodeQueryStringKeys' => true,
                'expectedConvertIdnToUtf8' => false,
            ],
            'enable' => [
                'fullyEncodeQueryStringKeys' => true,
                'convertIdnToUtf8' => true,
                'expectedFullyEncodeQueryStringKeys' => true,
                'expectedConvertIdnToUtf8' => true,
            ],
            'disable' => [
                'fullyEncodeQueryStringKeys' => false,
                'convertIdnToUtf8' => false,
                'expectedFullyEncodeQueryStringKeys' => false,
                'expectedConvertIdnToUtf8' => false,
            ],
        ];
    }
}
