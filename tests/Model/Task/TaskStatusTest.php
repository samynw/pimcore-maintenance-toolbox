<?php

namespace Samynw\MaintenanceToolboxBundle\Tests\Model\Task;

use Samynw\MaintenanceToolboxBundle\Exception\EmptyPropertyException;
use Samynw\MaintenanceToolboxBundle\Model\Task\TaskStatus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Key;

class TaskStatusTest extends TestCase
{
    public function testCanBeCreatedFromTaskName(): void
    {
        $taskname = 'dummy';
        $status = TaskStatus::fromTask($taskname);

        self::assertInstanceOf(TaskStatus::class, $status);
        self::assertEquals($taskname, $status->getTask());
    }

    public function testCannotBeCreatedFromEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TaskStatus::fromTask('');
    }

    public function testExpirationCanBeSet(): void
    {
        $status = TaskStatus::fromTask('dummy');
        $expiration = new \DateTimeImmutable('+1 hours ');
        $res = $status->setExpirationDate($expiration);

        self::assertInstanceOf(TaskStatus::class, $res); // fluent setter
        self::assertEquals($expiration, $status->getExpirationDate());
    }

    public function testExpirationCantBeEmpty()
    {
        $status = TaskStatus::fromTask('dummy');
        $this->expectException(EmptyPropertyException::class);
        $date = $status->getExpirationDate();
    }

    public function testExpirationIsNeededForDuration()
    {
        $status = TaskStatus::fromTask('dummy');
        $this->expectException(EmptyPropertyException::class);
        $date = $status->getDurationString();
    }

    public function testDurationCanBeCalculated(): void
    {
        $status = TaskStatus::fromTask('dummy')
            ->setExpirationDate(new \DateTimeImmutable('+1 hours '));
        self::assertIsInt($status->getDurationSeconds());
        self::assertIsString($status->getDurationString());
        self::assertMatchesRegularExpression('/[\d]{2}h[\d]{2}m[\d]{2}s/', $status->getDurationString());
    }

    public function testCanBeLocked()
    {
        $status = TaskStatus::fromTask('dummy');
        self::assertFalse($status->isLocked());
        $res = $status->setLocked(true);
        self::assertInstanceOf(TaskStatus::class, $res); // fluent setter
        self::assertTrue($status->isLocked());
    }

    public function testCanGenerateKey()
    {
        $status = TaskStatus::fromTask('dummy');
        self::assertInstanceOf(Key::class, $status->getKey());
        self::assertStringContainsString($status->getTask(), (string)$status->getKey());
    }
}
