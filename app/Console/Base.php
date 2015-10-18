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
 * @property \Kanboard\Model\Notification               $notification
 * @property \Kanboard\Model\Project                    $project
 * @property \Kanboard\Model\ProjectPermission          $projectPermission
 * @property \Kanboard\Model\ProjectAnalytic            $projectAnalytic
 * @property \Kanboard\Model\ProjectDailyColumnStats    $projectDailyColumnStats
 * @property \Kanboard\Model\ProjectDailyStats          $projectDailyStats
 * @property \Kanboard\Model\SubtaskExport              $subtaskExport
 * @property \Kanboard\Model\OverdueNotification        $overdueNotification
 * @property \Kanboard\Model\Task                       $task
 * @property \Kanboard\Model\TaskExport                 $taskExport
 * @property \Kanboard\Model\TaskFinder                 $taskFinder
 * @property \Kanboard\Model\Transition                 $transition
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
