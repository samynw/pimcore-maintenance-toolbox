<?php

namespace MaintenanceToolboxBundle\Model\Task;

use MaintenanceToolboxBundle\Exception\EmptyPropertyException;
use Symfony\Component\Lock\Key;

class Status
{
    /** @var string */
    private $task;
    /** @var bool */
    private $locked = false;
    /** @var \DateTimeImmutable */
    private $expirationDate;

    /**
     * Status constructor.
     *
     * @param string $task Name of the maintenance task
     */
    public function __construct(string $task)
    {
        if (empty($task)) {
            throw new \InvalidArgumentException(
                'No task name was given for the job'
            );
        }

        $this->task = $task;
    }

    /**
     * Generate a Status based on the taskname
     *
     * @param string $task
     * @return static
     */
    public static function fromTask(string $task): self
    {
        return new self($task);
    }

    /**
     * Return the name of the maintenance task
     *
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * Return the state of the lock
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Return the date when the lock will be released
     *
     * @return \DateTimeImmutable
     * @throws EmptyPropertyException
     */
    public function getExpirationDate(): \DateTimeImmutable
    {
        if ($this->expirationDate instanceof \DateTimeImmutable) {
            return $this->expirationDate;
        }

        throw EmptyPropertyException::forProperty('expirationDate');
    }

    /**
     * @param bool $locked
     * @return Status
     */
    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $expirationDate
     * @return Status
     */
    public function setExpirationDate(\DateTimeImmutable $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * Return the number of seconds the job was locked
     *
     * @return int
     */
    public function getDurationSeconds(): int
    {
        // By default the locks are set for 24 hours,
        // so the start time will be 1 day before the expiration
        $started = $this->getExpirationDate()->sub(new \DateInterval('P1D'));
        return time() - $started->getTimestamp();
    }

    /**
     * Human readable output of the time the job was locked
     *
     * @return string
     */
    public function getDurationString(): string
    {
        $duration = $this->getDurationSeconds();
        return sprintf('%02dh%02dm%02ds', ($duration / 3600), ($duration / 60 % 60), $duration % 60);
    }

    /**
     * Generate the according key for the maintenance job
     *
     * @return Key
     */
    public function getKey(): Key
    {
        return new Key('maintenance-' . $this->getTask());
    }
}
