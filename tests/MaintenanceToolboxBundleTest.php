<?php

namespace MaintenanceToolboxBundle\Tests;

use MaintenanceToolboxBundle\MaintenanceToolboxBundle;
use Pimcore\Test\KernelTestCase;

class MaintenanceToolboxBundleTest extends KernelTestCase
{
    /** @var MaintenanceToolboxBundle  */
    private $bundle;

    protected function setUp(): void
    {
        $this->bundle = new MaintenanceToolboxBundle();
    }

    public function testHasConfigurationFrame()
    {
        $iframePath = $this->bundle->getAdminIframePath();
        self::assertIsString($iframePath);
        self::assertStringContainsString('maintenance', $iframePath);
        self::assertStringContainsString('config', $iframePath);
    }

    public function testHasVersionDefined()
    {
        $version = $this->bundle->getVersion();
        self::assertIsString($version);
        self::assertNotEmpty($version);
    }
}
