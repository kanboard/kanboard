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
 * @property \Kanboard\Export\SubtaskExport                             $subtaskExport
 * @property \Kanboard\Export\TaskExport                                $taskExport
 * @property \Kanboard\Export\TransitionExport                          $transitionExport
 * @property \Kanboard\Model\Notification                               $notification
 * @property \Kanboard\Model\Project                                    $project
 * @property \Kanboard\Model\ProjectPermission                          $projectPermission
 * @property \Kanboard\Model\ProjectDailyColumnStats                    $projectDailyColumnStats
 * @property \Kanboard\Model\ProjectDailyStats                          $projectDailyStats
 * @property \Kanboard\Model\Task                                       $task
 * @property \Kanboard\Model\TaskFinder                                 $taskFinder
 * @property \Kanboard\Model\UserNotification                           $userNotification
 * @property \Kanboard\Model\UserNotificationFilter                     $userNotificationFilter
 * @property \Symfony\Component\EventDispatcher\EventDispatcher         $dispatcher
 */
abstract class Base extends Command
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
