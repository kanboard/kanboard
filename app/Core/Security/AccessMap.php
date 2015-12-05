<?php

namespace Kanboard\Core\Security;

/**
 * Access Map Definition
 *
 * @package  security
 * @author   Frederic Guillot
 */
class AccessMap
{
    /**
     * Default role
     *
     * @access private
     * @var string
     */
    private $defaultRole = '';

    /**
     * Access map
     *
     * @access private
     * @var array
     */
    private $map = array();

    /**
     * Define the default role when nothing match
     *
     * @access public
     * @param  string $role
     * @return Acl
     */
    public function setDefaultRole($role)
    {
        $this->defaultRole = $role;
        return $this;
    }

    /**
     * Add new access rules
     *
     * @access public
     * @param  string $controller
     * @param  string $method
     * @param  array  $roles
     * @return Acl
     */
    public function add($controller, $method, array $roles)
    {
        $controller = strtolower($controller);
        $method = strtolower($method);

        if (! isset($this->map[$controller])) {
            $this->map[$controller] = array();
        }

        if (! isset($this->map[$controller][$method])) {
            $this->map[$controller][$method] = array();
        }

        $this->map[$controller][$method] = $roles;

        return $this;
    }

    /**
     * Get roles that match the given controller/method
     *
     * @access public
     * @param  string $controller
     * @param  string $method
     * @return boolean
     */
    public function getRoles($controller, $method)
    {
        $controller = strtolower($controller);
        $method = strtolower($method);

        if (isset($this->map[$controller][$method])) {
            return $this->map[$controller][$method];
        }

        if (isset($this->map[$controller]['*'])) {
            return $this->map[$controller]['*'];
        }

        return array($this->defaultRole);
    }
}
