<?php

namespace Samynw\MaintenanceToolboxBundle\Service;

use Samynw\MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use Samynw\MaintenanceToolboxBundle\Service\Store\Adapter\AdapterInterface;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\PersistingStoreInterface;

class LockManipulator
{
    /** @var AdapterInterface */
    private $storeAdapter;
    /** @var ApplicationLogger */
    private $applicationLogger;

    /**
     * LockManipulator constructor.
     *
     * @param ApplicationLogger $applicationLogger
     * @param PersistingStoreInterface $store Use this to select the store adapter
     * @param iterable|AdapterInterface[] $storeAdapters Store adapters are services that will fetch stored more of lock
     */
    public function __construct(
        ApplicationLogger $applicationLogger,
        PersistingStoreInterface $store,
        iterable $storeAdapters
    ) {
        $this->applicationLogger = $applicationLogger;

        // Select the correct persistent store adapter
        foreach ($storeAdapters as $adapter) {
            if ($adapter->getStoreClassName() === \get_class($store)) {
                $this->storeAdapter = $adapter;
            }
        }
    }

    /**
     * Release a job lock from the persisting store
     *
     * @param string $job
     * @return bool
     * @throws LockNotFoundInStoreException
     */
    public function release(string $job): bool
    {
        $key = new Key('maintenance-' . $job);
        $rows = $this->storeAdapter->releaseLockByKey($key);

        if ($rows === 0) {
            throw LockNotFoundInStoreException::forKey($key);
        }

        // Write this to the application logger for history
        $this->applicationLogger->warning(
            sprintf(
                'Job lock for maintenance task "%s" has been explicitly released',
                \str_replace('maintenance-', '', (string)$key)
            ),
            ['component' => 'maintenance']
        );

        return true;
    }
}
