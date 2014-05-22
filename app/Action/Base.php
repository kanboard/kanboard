<?php

namespace Action;

use Core\Listener;

/**
 * Base class for automatic actions
 *
 * @package action
 * @author  Frederic Guillot
 */
abstract class Base implements Listener
{
    /**
     * Project id
     *
     * @access private
     * @var integer
     */
    private $project_id = 0;

    /**
     * User parameters
     *
     * @access private
     * @var array
     */
    private $params = array();

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
     * Constructor
     *
     * @access public
     * @param  integer  $project_id  Project id
     */
    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * Set an user defined parameter
     *
     * @access public
     * @param  string  $name    Parameter name
     * @param  mixed   $value   Value
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Get an user defined parameter
     *
     * @access public
     * @param  string  $name            Parameter name
     * @param  mixed   $default_value   Default value
     * @return mixed
     */
    public function getParam($name, $default_value = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default_value;
    }

    /**
     * Check if an action is executable (right project and required parameters)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action is executable
     */
    public function isExecutable(array $data)
    {
        if (isset($data['project_id']) && $data['project_id'] == $this->project_id && $this->hasRequiredParameters($data)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the event data has required parameters to execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if all keys are there
     */
    public function hasRequiredParameters(array $data)
    {
        foreach ($this->getEventRequiredParameters() as $parameter) {
            if (! isset($data[$parameter])) return false;
        }

        return true;
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        if ($this->isExecutable($data)) {
            return $this->doAction($data);
        }

        return false;
    }
}
