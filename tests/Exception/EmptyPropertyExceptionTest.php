<?php

namespace Samynw\MaintenanceToolboxBundle\Tests\Exception;

use Samynw\MaintenanceToolboxBundle\Exception\EmptyPropertyException;
use PHPUnit\Framework\TestCase;

class EmptyPropertyExceptionTest extends TestCase
{
    public function testCanExceptionBeCreatedByProperty()
    {
        $property = 'qmefjmqlkjf';
        $exception = EmptyPropertyException::forProperty($property);
        self::assertInstanceOf(EmptyPropertyException::class, $exception);
        self::assertStringContainsString($property, $exception->getMessage());
    }
}
