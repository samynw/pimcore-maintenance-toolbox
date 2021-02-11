<?php

namespace Samynw\MaintenanceToolboxBundle\Service\Store\Adapter;

use Doctrine\DBAL\Statement;
use Samynw\MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use PHPUnit\Framework\TestCase;
use Pimcore\Db\Connection;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\Store\PdoStore;

class PdoAdapterTest extends TestCase
{
    /** @var PdoAdapter */
    private $adapter;
    /** @var Key */
    private $key;

    protected function setUp(): void
    {
        $this->adapter = new PdoAdapter($this->createMock(Connection::class));
        $this->key = new Key('dummykey');
    }

    private function mockStatement()
    {
        $statementMock = $this->createMock(Statement::class);
        $statementMock->method('bindValue');
        $statementMock->method('execute');
        return $statementMock;
    }

    public function testReturnsStoreClass()
    {
        self::assertEquals(PdoStore::class, $this->adapter->getStoreClassName());
    }

    public function testKeyCanBeGenerated()
    {
        // Use reflection to test private method until the integration tests are up and running
        $reflection = new \ReflectionClass(get_class($this->adapter));
        $method = $reflection->getMethod('generateKeyId');
        $method->setAccessible(true);

        self::assertIsString($method->invokeArgs($this->adapter, [$this->key]));
    }

    public function testThrowsExceptonOnLockNotFound()
    {
        $statementMock = $this->mockStatement();
        $statementMock->method('rowCount')->willReturn(0);
        $connectionMock = $this->createMock(Connection::class);
        $connectionMock->method('prepare')->willReturn($statementMock);

        $adapter = new PdoAdapter($connectionMock);
        $this->expectException(LockNotFoundInStoreException::class);
        $adapter->getExpirationByKey(new Key('dummy'));
    }

    public function testThrowsExceptionOnLockWithoutExpirationDate()
    {
        $statementMock = $this->mockStatement();
        $statementMock->method('rowCount')->willReturn(1);
        $connectionMock = $this->createMock(Connection::class);
        $connectionMock->method('prepare')->willReturn($statementMock);

        $adapter = new PdoAdapter($connectionMock);
        $this->expectException(LockNotFoundInStoreException::class);
        $adapter->getExpirationByKey(new Key('dummy'));
    }

    public function testCanFetchExpirationDate()
    {
        $statementMock = $this->mockStatement();
        $statementMock->method('rowCount')->willReturn(1);
        $statementMock->method('fetch')->willReturn(['key_expiration' => time()]);
        $connectionMock = $this->createMock(Connection::class);
        $connectionMock->method('prepare')->willReturn($statementMock);

        $adapter = new PdoAdapter($connectionMock);
        self::assertInstanceOf(
            \DateTimeImmutable::class,
            $adapter->getExpirationByKey(new Key('dummy'))
        );
    }

    public function testCanReleaseLock()
    {
        $statementMock = $this->mockStatement();
        $statementMock->method('rowCount')->willReturn(\mt_rand());
        $connectionMock = $this->createMock(Connection::class);
        $connectionMock->method('prepare')->willReturn($statementMock);

        $adapter = new PdoAdapter($connectionMock);
        self::assertIsInt($adapter->releaseLockByKey(new Key('dummy')));
    }
}
