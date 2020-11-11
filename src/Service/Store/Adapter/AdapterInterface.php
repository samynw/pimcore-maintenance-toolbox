<?php

namespace MaintenanceToolboxBundle\Service\Store\Adapter;

use Symfony\Component\Lock\Key;

interface AdapterInterface
{
    /**
     * Return the FQCN of the store this adapter is intended for.
     * This classname will be used to determine the correct adpater
     *
     * @return string
     */
    public function getStoreClassName(): string;

    /**
     * Fetch the lock details from store and return the expiration date
     *
     * @param Key $key
     * @return \DateTimeImmutable
     */
    public function getExpirationByKey(Key $key): \DateTimeImmutable;

    /**
     * Release the lock based on the key
     * Return the number of rows affected in the store
     *
     * If your store doesn't support the count of affected rows
     * - 0 for error
     * - 1 for success
     *
     * @param Key $key
     * @return int Return the number of rows that were affected by this action
     */
    public function releaseLockByKey(Key $key): int;
}
