<?php

namespace MaintenanceToolboxBundle\Tests\Config;

use MaintenanceToolboxBundle\Config\ToolboxConfig;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ToolboxConfigTest extends MockeryTestCase
{
    /** @var ToolboxConfig */
    private $config;

    protected function setUp(): void
    {
        // Must use Mockery because class methods are called statically
        $yamlMock = \Mockery::mock('overload:' . Yaml::class);
        $yamlMock->expects()->once()->shouldReceive('parseFile')->andReturn([
            'maintenancetoolbox' => [
                'release' => [
                    'enabled' => true,
                ],
            ]
        ]);
        $yamlMock->expects()->once()->shouldReceive('dump')->andReturn();

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

    public function testFallsBackToEmptyConfig()
    {
        \Mockery::close();// teardown the setup() because we'll use an other file parsing mock for this test
        $yamlMock = \Mockery::mock('overload:' . Yaml::class);
        $yamlMock->expects()->once()->shouldReceive('parseFile')
            ->andThrow(new ParseException('For testing'));

        $notFoundConfig = new ToolboxConfig('dummyPath');
        self::assertIsArray($notFoundConfig->toArray());
        self::assertEmpty($notFoundConfig->toArray());
    }

    public function testFallsBackToEmptyConfigIfFileEmpty()
    {
        \Mockery::close();// teardown the setup() because we'll use an other file parsing mock for this test
        $yamlMock = \Mockery::mock('overload:' . Yaml::class);
        $yamlMock->expects()->once()->shouldReceive('parseFile')
            ->andReturn([]);

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
        $testData = $this->config->toArray();
        // Set the enabled state of a feature
        $testData[ToolboxConfig::FEATURE_RELEASE]['enabled'] = false;

        // Method returns void, so assert null
        self::assertNull($this->config->save($testData));
        // Only tests unit, no integration (so not if value got stored)
    }
}
