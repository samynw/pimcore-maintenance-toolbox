<?php

namespace MaintenanceToolboxBundle\Service;

use MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use MaintenanceToolboxBundle\Service\Store\Adapter\AdapterInterface;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\PersistingStoreInterface;

class LockManipulator
{
    /** @var AdapterInterface */
    private $storeAdapter;

    /**
     * LockManipulator constructor.
     *
     * @param PersistingStoreInterface $store Use this to select the store adapter
     * @param iterable|AdapterInterface[] $storeAdapters Store adapters are services that will fetch stored more of lock
     */
    public function __construct(
        PersistingStoreInterface $store,
        iterable $storeAdapters
    ) {
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

        return true;
    }
}
