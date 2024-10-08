<?php

namespace Kanboard\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ResetPasswordCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('user:reset-password')
            ->setDescription('Change user password')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $username = $input->getArgument('username');

        $passwordQuestion = new Question('What is the new password for '.$username.'? (characters are not printed)'.PHP_EOL);
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $passwordQuestion);

        $confirmationQuestion = new Question('Confirmation:'.PHP_EOL);
        $confirmationQuestion->setHidden(true);
        $confirmationQuestion->setHiddenFallback(false);

        $confirmation = $helper->ask($input, $output, $confirmationQuestion);

        if ($this->validatePassword($output, $password, $confirmation)) {
            $this->resetPassword($output, $username, $password);
        }
        return 0;
    }

    private function validatePassword(OutputInterface $output, $password, $confirmation)
    {
        list($valid, $errors) = $this->passwordResetValidator->validateModification(array(
            'password' => $password,
            'confirmation' => $confirmation,
        ));

        if (!$valid) {
            foreach ($errors as $error_list) {
                foreach ($error_list as $error) {
                    $output->writeln('<error>'.$error.'</error>');
                }
            }
        }

        return $valid;
    }

    private function resetPassword(OutputInterface $output, $username, $password)
    {
        $userId = $this->userModel->getIdByUsername($username);

        if (empty($userId)) {
            $output->writeln('<error>User not found</error>');
            return false;
        }

        if (!$this->userModel->update(array('id' => $userId, 'password' => $password))) {
            $output->writeln('<error>Unable to update password</error>');
            return false;
        }

        $output->writeln('<info>Password updated successfully</info>');

        return true;
    }
}
