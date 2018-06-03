<?php

namespace Kanboard\Action;

use Kanboard\Event\GenericEvent;

/**
 * Base class for automatic actions
 *
 * @package Kanboard\Action
 * @author  Frederic Guillot
 */
abstract class Base extends \Kanboard\Core\Base
{
    /**
     * Extended events
     *
     * @access private
     * @var array
     */
    private $compatibleEvents = array();

    /**
     * Keep history of executed events
     *
     * @access private
     * @var array
     */
    private $callStack = [];

    /**
     * Project id
     *
     * @access private
     * @var integer
     */
    private $projectId = 0;

    /**
     * User parameters
     *
     * @access private
     * @var array
     */
    private $params = array();

    /**
     * Get automatic action name
     *
     * @final
     * @access public
     * @return string
     */
    final public function getName()
    {
        return '\\'.get_called_class();
    }

    /**
     * Get automatic action description
     *
     * @abstract
     * @access public
     * @return string
     */
    abstract public function getDescription();

    /**
     * Execute the action
     *
     * @abstract
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    abstract public function doAction(array $data);

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @abstract
     * @access public
     * @return array
     */
    abstract public function getActionRequiredParameters();

    /**
     * Get the required parameter for the event (check if for the event data)
     *
     * @abstract
     * @access public
     * @return array
     */
    abstract public function getEventRequiredParameters();

    /**
     * Get the compatible events
     *
     * @abstract
     * @access public
     * @return array
     */
    abstract public function getCompatibleEvents();

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    abstract public function hasRequiredCondition(array $data);

    /**
     * Return class information
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        $params = array();

        foreach ($this->params as $key => $value) {
            $params[] = $key.'='.var_export($value, true);
        }

        return $this->getName().'('.implode('|', $params).')';
    }

    /**
     * Set project id
     *
     * @access public
     * @param  integer $project_id
     * @return Base
     */
    public function setProjectId($project_id)
    {
        $this->projectId = $project_id;
        return $this;
    }

    /**
     * Get project id
     *
     * @access public
     * @return integer
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set an user defined parameter
     *
     * @access  public
     * @param   string  $name    Parameter name
     * @param   mixed   $value   Value
     * @return  Base
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Get an user defined parameter
     *
     * @access public
     * @param  string  $name            Parameter name
     * @param  mixed   $default         Default value
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    /**
     * Check if an action is executable (right project and required parameters)
     *
     * @access public
     * @param  array   $data
     * @param  string  $eventName
     * @return bool
     */
    public function isExecutable(array $data, $eventName)
    {
        return $this->hasCompatibleEvent($eventName) &&
               $this->hasRequiredProject($data) &&
               $this->hasRequiredParameters($data) &&
               $this->hasRequiredCondition($data);
    }

    /**
     * Check if the event is compatible with the action
     *
     * @access public
     * @param  string  $eventName
     * @return bool
     */
    public function hasCompatibleEvent($eventName)
    {
        return in_array($eventName, $this->getEvents());
    }

    /**
     * Check if the event data has the required project
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredProject(array $data)
    {
        return (isset($data['project_id']) && $data['project_id'] == $this->getProjectId()) ||
            (isset($data['task']['project_id']) && $data['task']['project_id'] == $this->getProjectId());
    }

    /**
     * Check if the event data has required parameters to execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if all keys are there
     */
    public function hasRequiredParameters(array $data, array $parameters = array())
    {
        $parameters = $parameters ?: $this->getEventRequiredParameters();

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                return isset($data[$key]) && $this->hasRequiredParameters($data[$key], $value);
            } else if (! isset($data[$value])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  \Kanboard\Event\GenericEvent   $event
     * @param  string                         $eventName
     * @return bool
     */
    public function execute(GenericEvent $event, $eventName)
    {
        $data = $event->getAll();
        $hash = md5(serialize($data).$eventName);

        // Do not call twice the same action with the same arguments.
        if (isset($this->callStack[$hash])) {
            return false;
        } else {
            $this->callStack[$hash] = true;
        }

        $executable = $this->isExecutable($data, $eventName);
        $executed = false;

        if ($executable) {
            $executed = $this->doAction($data);
        }

        $this->logger->debug($this.' ['.$eventName.'] => executable='.var_export($executable, true).' exec_success='.var_export($executed, true));

        return $executed;
    }

    /**
     * Register a new event for the automatic action
     *
     * @access public
     * @param  string $event
     * @param  string $description
     * @return Base
     */
    public function addEvent($event, $description = '')
    {
        if ($description !== '') {
            $this->eventManager->register($event, $description);
        }

        $this->compatibleEvents[] = $event;
        return $this;
    }

    /**
     * Get all compatible events of an automatic action
     *
     * @access public
     * @return array
     */
    public function getEvents()
    {
        return array_unique(array_merge($this->getCompatibleEvents(), $this->compatibleEvents));
    }
}
