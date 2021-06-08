<?php

namespace Samynw\MaintenanceToolboxBundle\Tests;

use Samynw\MaintenanceToolboxBundle\MaintenanceToolboxBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MaintenanceToolboxBundleTest extends KernelTestCase
{
    /** @var MaintenanceToolboxBundle  */
    private $bundle;

    protected static function createKernel(array $options = [])
    {
        $kernel = parent::createKernel($options);

        \Pimcore::setKernel($kernel);

        return $kernel;
    }

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
