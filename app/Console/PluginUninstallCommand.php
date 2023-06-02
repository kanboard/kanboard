<?php

namespace Kanboard\Console;

use Kanboard\Core\Plugin\Installer;
use Kanboard\Core\Plugin\PluginInstallerException;
use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginUninstallCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('plugin:uninstall')
            ->setDescription('Remove a plugin')
            ->addArgument('pluginId', InputArgument::REQUIRED, 'Plugin directory name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Installer::isConfigured()) {
            throw new LogicException('Kanboard is not configured to install plugins itself');
        }

        try {
            $installer = new Installer($this->container);
            $installer->uninstall($input->getArgument('pluginId'));
            $output->writeln('<info>Plugin removed successfully</info>');
            return 0;
        } catch (PluginInstallerException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return 1;
        }
    }
}
