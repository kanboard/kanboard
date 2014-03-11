<?php

namespace Action;

abstract class Base implements \Core\Listener
{
    private $project_id = 0;
    private $params = array();

    abstract public function doAction(array $data);
    abstract public function getActionRequiredParameters();
    abstract public function getEventRequiredParameters();

    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function getParam($name, $default_value = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default_value;
    }

    public function isExecutable(array $data)
    {
        if (isset($data['project_id']) && $data['project_id'] == $this->project_id && $this->hasRequiredParameters($data)) {
            return true;
        }

        return false;
    }

    public function hasRequiredParameters(array $data)
    {
        foreach ($this->getEventRequiredParameters() as $parameter) {
            if (! isset($data[$parameter])) return false;
        }

        return true;
    }

    public function execute(array $data)
    {
        if ($this->isExecutable($data)) {
            return $this->doAction($data);
        }

        return false;
    }
}
