<?php

namespace Kanboard\Console;

use Kanboard\Core\Plugin\Installer;
use Kanboard\Core\Plugin\PluginInstallerException;
use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginInstallCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('plugin:install')
            ->setDescription('Install a plugin from a remote Zip archive')
            ->addArgument('url', InputArgument::REQUIRED, 'Archive URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Installer::isConfigured()) {
            throw new LogicException('Kanboard is not configured to install plugins itself');
        }

        try {
            $installer = new Installer($this->container);
            $installer->install($input->getArgument('url'));
            $output->writeln('<info>Plugin installed successfully</info>');
        } catch (PluginInstallerException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }
}
