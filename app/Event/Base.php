<?php

namespace Event;

use Pimple\Container;
use Core\Listener;
use Core\Tool;

/**
 * Base Listener
 *
 * @package event
 * @author  Frederic Guillot
 *
 * @property \Model\Comment            $comment
 * @property \Model\Project            $project
 * @property \Model\ProjectActivity    $projectActivity
 * @property \Model\SubTask            $subTask
 * @property \Model\Task               $task
 * @property \Model\TaskFinder         $taskFinder
 */
abstract class Base implements Listener
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
     * @param  \Pimple\Container    $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Return class information
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return get_called_class();
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

    /**
     * Get event namespace
     *
     * Event = task.close | Namespace = task
     *
     * @access public
     * @return string
     */
    public function getEventNamespace()
    {
        $event_name = $this->container['event']->getLastTriggeredEvent();
        return substr($event_name, 0, strpos($event_name, '.'));
    }
}
