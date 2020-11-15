<?php

namespace MaintenanceToolboxBundle\Tests\Exception;

use MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Key;

class LockNotFoundInStoreExceptionTest extends TestCase
{
    public function testCanExceptionBeCreatedByProperty()
    {
        $key = 'qmefjmqlkjf';
        $exception = LockNotFoundInStoreException::forKey(new Key($key));
        self::assertInstanceOf(LockNotFoundInStoreException::class, $exception);
        self::assertStringContainsString($key, $exception->getMessage());
    }
}
