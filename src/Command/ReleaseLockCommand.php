<?php

namespace MaintenanceToolboxBundle\Command;

use MaintenanceToolboxBundle\Exception\LockNotFoundInStoreException;
use MaintenanceToolboxBundle\Service\LockManipulator;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReleaseLockCommand extends AbstractCommand
{
    /** @var LockManipulator */
    private $lockManipulator;

    public function __construct(LockManipulator $lockManipulator)
    {
        $this->lockManipulator = $lockManipulator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('maintenance:release-lock')
            ->setDescription('Release the lock from a maintenance task')
            ->addArgument('task', InputArgument::REQUIRED, 'Name of the task you want to unlock');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $taskToUnlock = $this->input->getArgument('task');

        $io = new SymfonyStyle($input, $output);
        $io->caution([strtoupper('This might be an unsafe operation.')]);
        $io->note([
            "You've requested to remove the lock from a maintenance job.",
            "In normal circumstances this should never be done manually.",
            "Removing a job lock, might lead to concurring processes and unexpected behaviour.",
            "Do not continue unless you fully comprehend the possible consequences of this action.",
        ]);

        $question = sprintf(
            'Are you sure you want to release the job lock for "%s"? (y/n) ',
            $taskToUnlock
        );
        if ($io->confirm($question, false)) {
            try {
                $this->lockManipulator->release($taskToUnlock);
                $io->success(sprintf('Job "%s" has been unlocked', $taskToUnlock));
            } catch (LockNotFoundInStoreException $e) {
                $io->error($e->getMessage());
                $io->writeln([
                    'The lock might have been released before running this command.',
                    'Please check the current state.',
                ]);
                $io->newLine();
                return 1;
            }
        } else {
            $io->writeln('User has aborted the command');
            $io->newLine();
        }
        return 0;
    }
}
