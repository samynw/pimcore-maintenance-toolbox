<?php

namespace MaintenanceToolboxBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\FetchMode;
use MaintenanceToolboxBundle\Model\Task\Status;
use MaintenanceToolboxBundle\Service\TaskListing;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Maintenance\Executor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory as LockFactory;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\PdoStore;

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
        $this
            ->setName('maintenance:list')
            ->setDescription('List the maintenance jobs')
            ->addOption('locked', null, null, 'Only show locked tasks');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['Maintenance task', 'Locked']);

        foreach ($this->fetchTasks() as $job) {
            $table->addRow([
                $job->getTask(),
                $this->formatBool($job->isLocked()),
            ]);
        }
        $table->render();

        return 0;
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
}
