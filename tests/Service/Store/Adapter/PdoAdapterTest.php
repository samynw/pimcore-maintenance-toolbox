<?php

namespace MaintenanceToolboxBundle\Service\Store\Adapter;

use PHPUnit\Framework\TestCase;
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
        $this->adapter = new PdoAdapter();
        $this->key = new Key('dummykey');
    }

    public function testReturnsStoreClass()
    {
        self::assertEquals(PdoStore::class, $this->adapter->getStoreClassName());
    }

    public function testKeyCanBeGenerated()
    {
        // Use refeflection to test private method until the integration tests are up and running
        $reflection = new \ReflectionClass(get_class($this->adapter));
        $method = $reflection->getMethod('generateKeyId');
        $method->setAccessible(true);

        self::assertIsString($method->invokeArgs($this->adapter, [$this->key]));
    }
}
