<?php

namespace MaintenanceToolboxBundle\Tests\Config;

use MaintenanceToolboxBundle\Config\ToolboxConfig;
use PHPUnit\Framework\TestCase;

class ToolboxConfigTest extends TestCase
{
    /** @var ToolboxConfig */
    private $config;

    protected function setUp(): void
    {
        if (!defined('PIMCORE_CONFIGURATION_DIRECTORY')) {
            define('PIMCORE_CONFIGURATION_DIRECTORY', __DIR__ . '/../../../../../var/config');
        }
        if (!defined('PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY')) {
            define('PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY', __DIR__ . '/../../../../../app/config/pimcore');
        }

        $this->config = new ToolboxConfig();
    }

    public function testIsPathReturned()
    {
        self::assertIsString(ToolboxConfig::getConfigFilePath());
        self::assertEquals('maintenance-toolbox.yml', ToolboxConfig::CONFIG_FILENAME);
    }

    public function testIsConfigFound()
    {
        $data = $this->config->toArray();
        self::assertIsArray($data);
        self::assertGreaterThan(0, count($data));
    }

    public function testFallsBacktoEmptyConfig()
    {
        $notFoundConfig = new ToolboxConfig('dummyPath');
        self::assertIsArray($notFoundConfig->toArray());
        self::assertEmpty($notFoundConfig->toArray());
    }

    public function testCanCheckFeatureFlags()
    {
        // don't check true or false, just return type
        self::assertIsBool($this->config->isFeatureEnabled(ToolboxConfig::FEATURE_RELEASE));
        // test with dummy feature
        self::assertEquals(false, $this->config->isFeatureEnabled('qmdlkfjqmsldfkj'));
    }

    public function testCanConfigBeStored()
    {
        $origData = $this->config->toArray();
        $testData = $this->config->toArray();
        // Set the enabled state of a feature and check if the feature check matches
        $testData[ToolboxConfig::FEATURE_RELEASE]['enabled'] = false;
        $this->config->save($testData);
        self::assertEquals(false, $this->config->isFeatureEnabled(ToolboxConfig::FEATURE_RELEASE));
        // Set new value to check if the update has worked
        $testData[ToolboxConfig::FEATURE_RELEASE]['enabled'] = true;
        $this->config->save($testData);
        self::assertEquals(true, $this->config->isFeatureEnabled(ToolboxConfig::FEATURE_RELEASE));

        $this->config->save($origData);
        self::assertEquals($origData, $this->config->toArray());
    }
}
