<?php

namespace MaintenanceToolboxBundle\Tests\Service\FormBuilder;

use MaintenanceToolboxBundle\Config\ToolboxConfig;
use MaintenanceToolboxBundle\Form\ConfigType;
use MaintenanceToolboxBundle\Service\FormBuilder\EditConfig;
use PHPUnit\Framework\TestCase;

class EditConfigTest extends TestCase
{
    /** @var EditConfig */
    private $service;

    protected function setUp(): void
    {
        $config = $this->createMock(ToolboxConfig::class);
        $config->method('toArray')->willReturn([
            'release' => [
                'enabled' => true,
            ]
        ]);
        $this->service = new EditConfig($config);
    }

    public function testReturnsFormClass()
    {
        $class = $this->service->getFormClassName();
        self::assertIsString($class);
        self::assertEquals(ConfigType::class, $class);
    }

    public function testHasDefaultOptions()
    {
        self::assertIsArray($this->service->getDefaultOptions());
    }

    public function testHasDefaultValues()
    {
        self::assertIsArray($this->service->getDefaultValues());
    }
}
