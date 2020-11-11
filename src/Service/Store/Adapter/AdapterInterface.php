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
}
