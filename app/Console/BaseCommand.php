<?php

namespace Kanboard\Console;

use Pimple\Container;
use Symfony\Component\Console\Command\Command;

/**
 * Base command class
 *
 * @package  console
 * @author   Frederic Guillot
 *
 * @property \Kanboard\Validator\PasswordResetValidator         $passwordResetValidator
 * @property \Kanboard\Export\SubtaskExport                     $subtaskExport
 * @property \Kanboard\Export\TaskExport                        $taskExport
 * @property \Kanboard\Export\TransitionExport                  $transitionExport
 * @property \Kanboard\Model\NotificationModel                  $notificationModel
 * @property \Kanboard\Model\ProjectModel                       $projectModel
 * @property \Kanboard\Model\ProjectPermissionModel             $projectPermissionModel
 * @property \Kanboard\Model\ProjectDailyColumnStatsModel       $projectDailyColumnStatsModel
 * @property \Kanboard\Model\ProjectDailyStatsModel             $projectDailyStatsModel
 * @property \Kanboard\Model\TaskModel                          $taskModel
 * @property \Kanboard\Model\TaskFinderModel                    $taskFinderModel
 * @property \Kanboard\Model\UserModel                          $userModel
 * @property \Kanboard\Model\UserNotificationModel              $userNotificationModel
 * @property \Kanboard\Model\UserNotificationFilterModel        $userNotificationFilterModel
 * @property \Kanboard\Model\ProjectUserRoleModel               $projectUserRoleModel
 * @property \Kanboard\Core\Plugin\Loader                       $pluginLoader
 * @property \Kanboard\Core\Http\Client                         $httpClient
 * @property \Kanboard\Core\Queue\QueueManager                  $queueManager
 * @property \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
 */
abstract class BaseCommand extends Command
{
    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    /**
     * Load automatically models
     *
     * @access public
     * @param  string $name Model name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }
}
