<?php

namespace Kanboard\Console;

use Kanboard\Core\Plugin\Base as BasePlugin;
use Kanboard\Core\Plugin\Directory;
use Kanboard\Core\Plugin\Installer;
use LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginUpgradeCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('plugin:upgrade')
            ->setDescription('Update all installed plugins')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Installer::isConfigured()) {
            throw new LogicException('Kanboard is not configured to install plugins itself');
        }

        $installer = new Installer($this->container);
        $availablePlugins = Directory::getInstance($this->container)->getAvailablePlugins();

        foreach ($this->pluginLoader->getPlugins() as $installedPlugin) {
            $pluginDetails = $this->getPluginDetails($availablePlugins, $installedPlugin);

            if ($pluginDetails === null) {
                $output->writeln('<error>* Plugin not available in the directory: '.$installedPlugin->getPluginName().'</error>');
            } elseif ($pluginDetails['version'] > $installedPlugin->getPluginVersion()) {
                $output->writeln('<comment>* Updating plugin: '.$installedPlugin->getPluginName().'</comment>');
                $installer->update($pluginDetails['download']);
            } else {
                $output->writeln('<info>* Plugin up to date: '.$installedPlugin->getPluginName().'</info>');
            }
        }
        return 0;
    }

    protected function getPluginDetails(array $availablePlugins, BasePlugin $installedPlugin)
    {
        foreach ($availablePlugins as $availablePluginName => $availablePlugin) {
            if ($availablePluginName === $installedPlugin->getPluginName()) {
                return $availablePlugin;
            }
        }

        return null;
    }
}
