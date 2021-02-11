<?php

namespace Samynw\MaintenanceToolboxBundle\Tests\Command;

use Samynw\MaintenanceToolboxBundle\Command\ReleaseLockCommand;
use Samynw\MaintenanceToolboxBundle\Config\ToolboxConfig;
use Samynw\MaintenanceToolboxBundle\Service\LockManipulator;
use PHPUnit\Framework\TestCase;

class ReleaseLockCommandTest extends TestCase
{
    public function testCanBeHidden()
    {
        $lockManipulator = $this->createMock(LockManipulator::class);

        $config = $this->createMock(ToolboxConfig::class);
        $config->method('isFeatureEnabled')->willReturn(false);

        $command = new ReleaseLockCommand($lockManipulator, $config);
        self::assertFalse($command->isEnabled());
    }

    public function testCanBeActive()
    {
        $lockManipulator = $this->createMock(LockManipulator::class);

        $config = $this->createMock(ToolboxConfig::class);
        $config->method('isFeatureEnabled')->willReturn(true);

        $command = new ReleaseLockCommand($lockManipulator, $config);
        self::assertTrue($command->isEnabled());
    }
}
