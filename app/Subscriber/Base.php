<?php

namespace Subscriber;

use Pimple\Container;

/**
 * Base subscriber class
 *
 * @package  subscriber
 * @author   Frederic Guillot
 *
 * @property \Model\Config                 $config
 * @property \Model\Notification           $notification
 * @property \Model\Project                $project
 * @property \Model\ProjectPermission      $projectPermission
 * @property \Model\ProjectAnalytic        $projectAnalytic
 * @property \Model\ProjectDailySummary    $projectDailySummary
 * @property \Model\Task                   $task
 * @property \Model\TaskExport             $taskExport
 * @property \Model\TaskFinder             $taskFinder
 * @property \Model\UserSession            $userSession
 */
abstract class Base
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
