<?php

namespace MaintenanceToolboxBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use MaintenanceToolboxBundle\Exception\EmptyPropertyException;
use MaintenanceToolboxBundle\Model\Task\TaskStatus;
use MaintenanceToolboxBundle\Service\TaskListing;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    /** @var TaskListing */
    private $taskResource;

    /**
     * @param TaskListing $taskResource
     */
    public function __construct(TaskListing $taskResource)
    {
        parent::__construct();
        $this->taskResource = $taskResource;
    }

    protected function configure()
    {
        $sortingOptions = TaskListing::SORTING_OPTIONS;

        $this
            ->setName('maintenance:list')
            ->setDescription('List the maintenance jobs')
            ->addOption('locked', null, null, 'Only show locked tasks')
            ->addOption(
                'sort',
                's',
                InputOption::VALUE_OPTIONAL,
                'Sorting of the list. Supported options: ["' . implode('", "', $sortingOptions) . '"]',
                reset($sortingOptions)
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tasks = $this->getList();
        $showExpirationColumns = $this->shouldShowExpirationColumns($tasks);

        $header = ['Maintenance task', 'Locked'];
        if ($showExpirationColumns) {
            $header[] = 'Lock expiration';
            $header[] = 'Duration';
        }

        $rows = [];
        foreach ($tasks as $job) {
            $row = [
                'name' => $job->getTask(),
                'locked' => $this->formatBool($job->isLocked()),
            ];

            // If needed, add the expiration columns
            if ($showExpirationColumns && $job->isLocked()) {
                try {
                    $row['expiration'] = $job->getExpirationDate()->format('Y-m-d H:i:s');
                    $row['duration'] = $job->getDurationString();
                } catch (EmptyPropertyException $e) {
                    // Locked but no timestamp found, must be some weird error
                    $row['expiration'] = $row['duration'] = 'ERR';
                }
            }

            $rows[] = $row;
        }

        $table = new Table($output);
        $table->setHeaders($header);
        $table->addRows($rows);
        $table->render();

        return 0;
    }

    /**
     * Get the list:
     * - fetch results
     * - sort list
     *
     * @return ArrayCollection|TaskStatus[]
     * @throws \Exception
     */
    private function getList(): ArrayCollection
    {
        // Validate options
        $sorting = $this->input->getOption('sort');
        $this->taskResource->validateSortingOption($sorting);

        $tasks = $this->fetchTasks();
        return $this->taskResource->sortTasks($tasks, $sorting);
    }

    /**
     * Fetch the (filtered) set of maintenance tasks
     *
     * @return ArrayCollection
     */
    private function fetchTasks(): ArrayCollection
    {
        if ($this->input->getOption('locked')) {
            return $this->taskResource->getLockedTasks();
        }

        return $this->taskResource->getTasks();
    }

    /**
     * Print a nice boolean value:
     * - green check or red cross if output can be decorated
     * - yes/no if output can't be decorated
     *
     * @param $state
     * @return string
     */
    private function formatBool($state): string
    {
        $decorated = $this->io->getOutput()->isDecorated();

        if ($state) {
            return sprintf(
                '<fg=green>%s</>',
                $decorated ? "\xE2\x9C\x94" : 'yes'
            );
        } else {
            return sprintf(
                '<fg=red>%s</>',
                $decorated ? "\xE2\x9D\x8C" : 'no'
            );
        }
    }

    /**
     * Check if the table should be limited to lock status or show the timestamps as well:
     * - at least one (locked) row should have an expiration date
     *
     * @param ArrayCollection|TaskStatus[] $tasks
     * @return bool
     */
    private function shouldShowExpirationColumns(ArrayCollection $tasks): bool
    {
        foreach ($tasks as $task) {
            // ignore tasks that aren't locked
            if (!$task->isLocked()) {
                continue;
            }

            try {
                if ($task->getExpirationDate() instanceof \DateTimeImmutable) {
                    // aha, we found one!
                    return true;
                }
            } catch (EmptyPropertyException $e) {
                // doesn't hold the predicate for this element
                continue;
            }
        }

        return false;
    }
}
