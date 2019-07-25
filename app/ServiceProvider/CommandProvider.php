<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Console\CronjobCommand;
use Kanboard\Console\DatabaseMigrationCommand;
use Kanboard\Console\DatabaseVersionCommand;
use Kanboard\Console\JobCommand;
use Kanboard\Console\LocaleComparatorCommand;
use Kanboard\Console\LocaleSyncCommand;
use Kanboard\Console\PluginInstallCommand;
use Kanboard\Console\PluginUninstallCommand;
use Kanboard\Console\PluginUpgradeCommand;
use Kanboard\Console\ProjectActivityArchiveCommand;
use Kanboard\Console\ProjectArchiveCommand;
use Kanboard\Console\ProjectDailyColumnStatsExportCommand;
use Kanboard\Console\ProjectDailyStatsCalculationCommand;
use Kanboard\Console\ResetPasswordCommand;
use Kanboard\Console\ResetTwoFactorCommand;
use Kanboard\Console\SubtaskExportCommand;
use Kanboard\Console\TaskExportCommand;
use Kanboard\Console\TaskOverdueNotificationCommand;
use Kanboard\Console\TaskTriggerCommand;
use Kanboard\Console\TransitionExportCommand;
use Kanboard\Console\VersionCommand;
use Kanboard\Console\WorkerCommand;
use Kanboard\Console\CssCommand;
use Kanboard\Console\JsCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Application;

/**
 * Class CommandProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class CommandProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * @param Container $container
     * @return Container
     */
    public function register(Container $container)
    {
        $application = new Application('Kanboard', APP_VERSION);
        $application->add(new TaskOverdueNotificationCommand($container));
        $application->add(new SubtaskExportCommand($container));
        $application->add(new TaskExportCommand($container));
        $application->add(new ProjectArchiveCommand($container));
        $application->add(new ProjectActivityArchiveCommand($container));
        $application->add(new ProjectDailyStatsCalculationCommand($container));
        $application->add(new ProjectDailyColumnStatsExportCommand($container));
        $application->add(new TransitionExportCommand($container));
        $application->add(new LocaleSyncCommand($container));
        $application->add(new LocaleComparatorCommand($container));
        $application->add(new TaskTriggerCommand($container));
        $application->add(new CronjobCommand($container));
        $application->add(new WorkerCommand($container));
        $application->add(new JobCommand($container));
        $application->add(new ResetPasswordCommand($container));
        $application->add(new ResetTwoFactorCommand($container));
        $application->add(new PluginUpgradeCommand($container));
        $application->add(new PluginInstallCommand($container));
        $application->add(new PluginUninstallCommand($container));
        $application->add(new DatabaseMigrationCommand($container));
        $application->add(new DatabaseVersionCommand($container));
        $application->add(new VersionCommand($container));
        $application->add(new CssCommand($container));
        $application->add(new JsCommand($container));

        $container['cli'] = $application;
        return $container;
    }
}
