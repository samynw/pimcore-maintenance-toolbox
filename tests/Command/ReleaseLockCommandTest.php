<?php

namespace MaintenanceToolboxBundle\Tests\Command;

use MaintenanceToolboxBundle\Command\ReleaseLockCommand;
use MaintenanceToolboxBundle\Service\LockManipulator;
use PHPUnit\Framework\TestCase;

class ReleaseLockCommandTest extends TestCase
{

    protected function setUp(): void
    {
        if (!defined('PIMCORE_CONFIGURATION_DIRECTORY')) {
            define('PIMCORE_CONFIGURATION_DIRECTORY', __DIR__ . '/../../../../../var/config');
        }
        if (!defined('PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY')) {
            define('PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY', __DIR__ . '/../../../../../app/config/pimcore');
        }
    }

    public function testCanGetFeatureFlag()
    {
        $lockManipulator = $this->createMock(LockManipulator::class);
        $command = new ReleaseLockCommand($lockManipulator);
        self::assertIsBool($command->isEnabled());
    }
}
