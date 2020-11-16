<?php

namespace MaintenanceToolboxBundle\Tests\Service\FormBuilder;

use MaintenanceToolboxBundle\Form\ConfigType;
use MaintenanceToolboxBundle\Service\FormBuilder\EditConfig;
use PHPUnit\Framework\TestCase;

class EditConfigTest extends TestCase
{
    /** @var EditConfig */
    private $service;

    protected function setUp(): void
    {
        $this->service = new EditConfig();
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
