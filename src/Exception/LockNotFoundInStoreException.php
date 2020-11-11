<?php

namespace MaintenanceToolboxBundle\Exception;

use Symfony\Component\Lock\Key;

class LockNotFoundInStoreException extends \RuntimeException
{
    /**
     * Create a LockNotFoundInStore exception for a specific key
     *
     * @param Key $key
     * @return LockNotFoundInStoreException
     */
    public static function forKey(Key $key): LockNotFoundInStoreException
    {
        return new self(sprintf(
            "No lock found in store for task %s",
            (string)$key
        ));
    }
}
