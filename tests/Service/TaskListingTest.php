<?php

namespace Samynw\MaintenanceToolboxBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Samynw\MaintenanceToolboxBundle\Model\Task\TaskStatus;
use Samynw\MaintenanceToolboxBundle\Service\Store\Adapter\PdoAdapter;
use Samynw\MaintenanceToolboxBundle\Service\TaskListing;
use PHPUnit\Framework\TestCase;
use Pimcore\Db\Connection;
use Pimcore\Maintenance\Executor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\PdoStore;

class TaskListingTest extends TestCase
{
    /** @var TaskListing */
    private $listing;

    protected function setUp(): void
    {
        $lockMock = $this->createMock(Factory::class);
        $executor = new Executor(
            'pidFileName',
            $this->createMock(LoggerInterface::class),
            $lockMock
        );
        $this->listing = new TaskListing(
            $executor,
            $lockMock,
            new PdoStore($this->createMock(Connection::class)),
            [new PdoAdapter($this->createMock(Connection::class))]
        );
    }

    public function testValidateSortingOption()
    {
        self::assertTrue($this->listing->validateSortingOption('name'));
        self::assertTrue($this->listing->validateSortingOption('lock'));

        $this->expectException(\InvalidArgumentException::class);
        self::assertTrue($this->listing->validateSortingOption('invalidkey'));
    }

    public function testCollectionsAreReturned()
    {
        // This doesn't test the contents itself
        self::assertInstanceOf(ArrayCollection::class, $this->listing->getTasks());
        self::assertInstanceOf(ArrayCollection::class, $this->listing->getLockedTasks());
    }

    public function testCollectionsCanBeSorted()
    {
        $tasks = $this->getTestData();

        $tasksByName = $this->listing->sortTasks($tasks, 'name');
        self::assertInstanceOf(ArrayCollection::class, $tasksByName);
        self::assertEquals('d', $tasksByName->last()->getTask());

        $tasksByLock = $this->listing->sortTasks($tasks, 'lock');
        self::assertInstanceOf(ArrayCollection::class, $tasksByLock);
        self::assertEquals('b', $tasksByLock->first()->getTask());
        self::assertEquals('c', $tasksByLock->last()->getTask()); // Because d is locked (without expiration date)

        $this->expectException(\InvalidArgumentException::class);
        self::assertInstanceOf(ArrayCollection::class, $this->listing->sortTasks($tasks, 'invalidkey'));
    }

    private function getTestData(): ArrayCollection
    {
        $task1 = TaskStatus::fromTask('a')
            ->setLocked(true)
            ->setExpirationDate(new \DateTimeImmutable('+4 hours'));
        $task2 = TaskStatus::fromTask('c');
        $task3 = TaskStatus::fromTask('d')->setLocked(true);
        $task4 = TaskStatus::fromTask('b')
            ->setLocked(true)
            ->setExpirationDate(new \DateTimeImmutable('+2 hours'));
        return new ArrayCollection([$task1, $task2, $task3, $task4]);
    }
}
