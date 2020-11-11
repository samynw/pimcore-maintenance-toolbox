<?php

namespace MaintenanceToolboxBundle\Command;

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
            ->setDescription('List the maintenance jobs');
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

        $tasks = $this->taskResource->getTasks();
        foreach ($tasks as $key => $job) {
            $table->addRow([
                $job->getTask(),
                $job->isLocked(),
            ]);
        }
        $table->render();

        return 0;
    }
}
