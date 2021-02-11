<?php

namespace Samynw\MaintenanceToolboxBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Samynw\MaintenanceToolboxBundle\Exception\EmptyPropertyException;
use Samynw\MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use Samynw\MaintenanceToolboxBundle\Model\Task\TaskStatus;
use Samynw\MaintenanceToolboxBundle\Service\Store\Adapter\AdapterInterface;
use Pimcore\Maintenance\Executor;
use Symfony\Component\Lock\Factory as LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;

class TaskListing
{
    const SORTING_OPTIONS = ['name', 'lock'];

    /** @var Executor */
    private $maintenanceExecutor;
    /** @var LockFactory */
    private $lockFactory;
    /** @var AdapterInterface */
    private $storeAdapter;

    /**
     * TastListing constructor.
     *
     * @param Executor $maintenanceExecutor The maintenance executor is needed  to fetch all tasks
     * @param LockFactory $lockFactory Needed to check the locks on tasks
     * @param PersistingStoreInterface $store Use this to select the store adapter
     * @param iterable|AdapterInterface[] $storeAdapters Store adapters are services that will fetch stored more of lock
     */
    public function __construct(
        Executor $maintenanceExecutor,
        LockFactory $lockFactory,
        PersistingStoreInterface $store,
        iterable $storeAdapters
    ) {
        $this->maintenanceExecutor = $maintenanceExecutor;
        $this->lockFactory = $lockFactory;

        // Select the correct persistent store adapter
        foreach ($storeAdapters as $adapter) {
            if ($adapter->getStoreClassName() === \get_class($store)) {
                $this->storeAdapter = $adapter;
            }
        }
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
            $status = TaskStatus::fromTask($taskName);
            // Create dummy lock of 0 seconds
            $lock = $this->lockFactory->createLock($status->getKey(), 0);
            // If the lock cannot be acquired, the job already is locked
            $status->setLocked(!$lock->acquire());

            // If the task is locked and there's a persistent store adapter
            // try to fetch the expiration date
            if ($this->storeAdapter instanceof AdapterInterface && $status->isLocked()) {
                try {
                    $status->setExpirationDate(
                        $this->storeAdapter->getExpirationByKey($status->getKey())
                    );
                } catch (LockNotFoundInStoreException $e) {
                    // Don't set the expiration date
                    // however this scenario might be fishy: locked but no expiration date?
                    // Might need to rethink this one
                }
            }
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
        return $tasks->filter(function (TaskStatus $jobStatus) {
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
                $compare = static function (TaskStatus $a, TaskStatus $b) {
                    return strcasecmp($a->getTask(), $b->getTask());
                };
                break;
            case 'lock':
                $compare = static function (TaskStatus $a, TaskStatus $b) {
                    // compare $b first so the locked jobs are at the top
                    $compareLock = $b->isLocked() <=> $a->isLocked();
                    if ($compareLock !== 0) {
                        return $compareLock;
                    }

                    try {
                        // longest duration to shortest
                        return $b->getDurationSeconds() <=> $a->getDurationSeconds();
                    } catch (EmptyPropertyException $e) {
                        // One or more items didn't have an expiration date,
                        // so continue to the fallback sorting
                    }

                    // If lock state is equal sort alphabetically
                    return strcasecmp($a->getTask(), $b->getTask());
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
