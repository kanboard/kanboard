<?php

namespace Console;

use Core\Tool;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;

/**
 * Base command class
 *
 * @package  console
 * @author   Frederic Guillot
 *
 * @property \Model\Notification           $notification
 * @property \Model\Project                $project
 * @property \Model\ProjectPermission      $projectPermission
 * @property \Model\ProjectAnalytic        $projectAnalytic
 * @property \Model\ProjectDailySummary    $projectDailySummary
 * @property \Model\Task                   $task
 * @property \Model\TaskExport             $taskExport
 * @property \Model\TaskFinder             $taskFinder
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
        return Tool::loadModel($this->container, $name);
    }
}
