<?php

namespace MaintenanceToolboxBundle\Tests;

use MaintenanceToolboxBundle\MaintenanceToolboxBundle;
use Pimcore\Test\KernelTestCase;

class MaintenanceToolboxBundleTest extends KernelTestCase
{
    public function testHasConfigurationFrame()
    {
        $bundle = new MaintenanceToolboxBundle();
        self::assertIsString($bundle->getAdminIframePath());
        self::assertStringContainsString('maintenance', $bundle->getAdminIframePath());
        self::assertStringContainsString('config', $bundle->getAdminIframePath());
    }
}
