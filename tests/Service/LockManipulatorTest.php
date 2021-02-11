<?php

namespace Samynw\MaintenanceToolboxBundle\Tests\Service;

use Samynw\MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use Samynw\MaintenanceToolboxBundle\Service\LockManipulator;
use Samynw\MaintenanceToolboxBundle\Service\Store\Adapter\PdoAdapter;
use PHPUnit\Framework\TestCase;
use Pimcore\Db\Connection;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Lock\Store\PdoStore;

class LockManipulatorTest extends TestCase
{
    private function mockAdapter()
    {
        $adapterMock = $this->createMock(PdoAdapter::class);
        $adapterMock->method('getStoreClassName')->willReturn(PdoStore::class);
        return $adapterMock;
    }

    public function testcanReleaseLock()
    {
        $adapterMock = $this->mockAdapter();
        $adapterMock->method('releaseLockByKey')->willReturn(1);

        $manipulator = new LockManipulator(
            new ApplicationLogger(),
            new PdoStore($this->createMock(Connection::class)),
            [$adapterMock]
        );
        self::assertIsBool($manipulator->release('dummy'));
    }

    public function testThrowsLockNotFoundException()
    {
        $adapterMock = $this->mockAdapter();
        $adapterMock->method('releaseLockByKey')->willReturn(0);

        $manipulator = new LockManipulator(
            new ApplicationLogger(),
            new PdoStore($this->createMock(Connection::class)),
            [$adapterMock]
        );
        $this->expectException(LockNotFoundInStoreException::class);
        self::assertIsBool($manipulator->release('dummy'));
    }
}
