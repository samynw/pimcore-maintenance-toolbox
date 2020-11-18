<?php

namespace MaintenanceToolboxBundle\Command;

use MaintenanceToolboxBundle\Config\ToolboxConfig;
use MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use MaintenanceToolboxBundle\Service\LockManipulator;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseLockCommand extends AbstractCommand
{
    /** @var LockManipulator */
    private $lockManipulator;
    /** @var ToolboxConfig */
    private $config;

    public function __construct(LockManipulator $lockManipulator, ToolboxConfig $config)
    {
        parent::__construct();
        $this->lockManipulator = $lockManipulator;
        $this->config = $config;
    }

    protected function configure()
    {
        $this
            ->setName('maintenance:release-lock')
            ->setDescription('Release the lock from a maintenance task')
            ->addArgument('task', InputArgument::REQUIRED, 'Name of the task you want to unlock');
    }

    /**
     * This command is only enabled if allowed in the config
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isFeatureEnabled($this->config::FEATURE_RELEASE);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $taskToUnlock = $this->input->getArgument('task');

        $this->io->caution([strtoupper('This might be an unsafe operation.')]);
        $this->io->note([
            "You've requested to remove the lock from a maintenance job.",
            "In normal circumstances this should never be done manually.",
            "Removing a job lock, might lead to concurring processes and unexpected behaviour.",
            "Do not continue unless you fully comprehend the possible consequences of this action.",
        ]);

        $question = sprintf(
            'Are you sure you want to release the job lock for "%s"? (y/n) ',
            $taskToUnlock
        );
        if ($this->io->confirm($question, false)) {
            try {
                $this->lockManipulator->release($taskToUnlock);
                $this->io->success(sprintf('Job "%s" has been unlocked', $taskToUnlock));
            } catch (LockNotFoundInStoreException $e) {
                $this->io->error($e->getMessage());
                $this->io->writeln([
                    'The lock might have been released before running this command.',
                    'Please check the current state.',
                ]);
                $this->io->newLine();
                return 1;
            }
        } else {
            $this->io->writeln('User has aborted the command');
            $this->io->newLine();
        }
        return 0;
    }
}
