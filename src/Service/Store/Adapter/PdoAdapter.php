<?php

namespace Samynw\MaintenanceToolboxBundle\Service\Store\Adapter;

use Samynw\MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use Pimcore\Db;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\Store\PdoStore;

class PdoAdapter implements AdapterInterface
{
    /** @var Db\Connection */
    private $connection;

    /**
     * PdoAdapter constructor.
     * @param Db\Connection $connection
     */
    public function __construct(Db\Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Use this adapter for the persistent PDO store
     *
     * @return string
     */
    public function getStoreClassName(): string
    {
        return PdoStore::class;
    }

    /**
     * Locks are stored in the lock_keys table
     *
     * @param Key $key
     * @return \DateTimeImmutable
     * @throws LockNotFoundInStoreException
     */
    public function getExpirationByKey(Key $key): \DateTimeImmutable
    {
        // Build query
        $sql = "SELECT * FROM lock_keys WHERE key_id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $this->generateKeyId($key));
        $result = $stmt->executeQuery();

        if ($result->rowCount() > 0) {
            $row = $result->fetchAssociative();
            if (!empty($row['key_expiration'])) {
                return (new \DateTimeImmutable())->setTimestamp($row['key_expiration']);
            }
        }

        // No record found or no expiration time set
        throw LockNotFoundInStoreException::forKey($key);
    }

    /**
     * Generate the ID used by the Pdo store
     *
     * @param Key $key
     * @return string
     */
    private function generateKeyId(Key $key): string
    {
        return hash('sha256', (string)$key);
    }

    /**
     * Release the lock based on the key
     *
     * @param Key $key
     * @return int Return the number of rows that were affected by this action
     */
    public function releaseLockByKey(Key $key): int
    {
        // Build query
        $sql = "DELETE FROM lock_keys WHERE key_id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $this->generateKeyId($key));
        $result = $stmt->executeQuery();

        return $result->rowCount() ?? 0;
    }
}
