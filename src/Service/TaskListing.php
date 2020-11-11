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
    const SORTING_OPTIONS = ['name', 'lock'];

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

    /**
     * Validate if the given option is supported
     *
     * @param string $option
     * @return bool
     */
    public function validateSortingOption(string $option): bool
    {
        if (\in_array($option, self::SORTING_OPTIONS)) {
            return true;
        }

        $msg = "The sorting option '%s' is not supported.\n";
        $msg .= "Please use one of the following options: '%s'";
        throw new \InvalidArgumentException(sprintf(
            $msg,
            $option,
            implode("', '", self::SORTING_OPTIONS)
        ));
    }

    /**
     * Sort the task resultlist
     *
     * @param ArrayCollection $tasks
     * @param string $sortingOption
     * @return ArrayCollection
     * @throws \Exception
     */
    public function sortTasks(ArrayCollection $tasks, string $sortingOption): ArrayCollection
    {
        switch ($sortingOption) {
            case 'name':
                $compare = static function (Status $a, Status $b) {
                    return strcasecmp($a->getTask(), $b->getTask());
                };
                break;
            case 'lock':
                $compare = static function (Status $a, Status $b) {
                    // compare $b first so the locked jobs are at the top
                    return $b->isLocked() <=> $a->isLocked();
                };
                break;
            default:
                throw new \InvalidArgumentException(
                    'Unsupported option ' . $sortingOption
                );
        }

        $iterator = $tasks->getIterator();
        $iterator->uasort($compare);
        return new ArrayCollection(\iterator_to_array($iterator));
    }
}
