<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetTwoFactorCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('user:reset-2fa')
            ->setDescription('Remove two-factor authentication for a user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $userId = $this->userModel->getIdByUsername($username);

        if (empty($userId)) {
            $output->writeln('<error>User not found</error>');
            return 1;
        }

        if (!$this->userModel->update(array('id' => $userId, 'twofactor_activated' => 0, 'twofactor_secret' => ''))) {
            $output->writeln('<error>Unable to update user profile</error>');
            return 1;
        }

        $output->writeln('<info>Two-factor authentication disabled</info>');
    }
}
