<?php

namespace MaintenanceToolboxBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use MaintenanceToolboxBundle\Model\Task\Status;
use Pimcore\Maintenance\Executor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory as LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;

class TaskListing
{
    /** @var Executor */
    private $maintenanceExecutor;
    /** @var LoggerInterface */
    private $logger;
    /** @var LockFactory */
    private $lockFactory;
    /** @var PersistingStoreInterface */
    private $store;

    /**
     * TastListing constructor.
     *
     * @param Executor $maintenanceExecutor
     * @param LoggerInterface $logger
     * @param LockFactory $lockFactory
     * @param PersistingStoreInterface $store
     */
    public function __construct(
        Executor $maintenanceExecutor,
        LoggerInterface $logger,
        LockFactory $lockFactory,
        PersistingStoreInterface $store
    ) {
        $this->maintenanceExecutor = $maintenanceExecutor;
        $this->logger = $logger;
        $this->lockFactory = $lockFactory;
        $this->store = $store;
    }

    /**
     * Return the full list of tasks, including their locked status
     *
     * @return ArrayCollection
     */
    public function getTasks(): ArrayCollection
    {
        $tasks = new ArrayCollection();

        foreach ($this->maintenanceExecutor->getTaskNames() as $taskName) {
            $status = Status::fromTask($taskName);
            // Create dummy lock of 0 seconds
            $lock = $this->lockFactory->createLock($status->getKey(), 0);
            // If the lock cannot be acquired, the job already is locked
            $status->setLocked(!$lock->acquire());

            // Add to the collection
            $tasks->add($status);
        }

        return $tasks;
    }

    /**
     * Return a filtered list of tasks, including their locked status
     * This will only return the locked tasks
     *
     * @return ArrayCollection
     */
    public function getLockedTasks(): ArrayCollection
    {
        $tasks = $this->getTasks();
        return $tasks->filter(function (Status $jobStatus) {
            return $jobStatus->isLocked();
        });
    }
}
