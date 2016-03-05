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
     * Role hierarchy
     *
     * @access private
     * @var array
     */
    private $hierarchy = array();

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
     * @return AccessMap
     */
    public function setDefaultRole($role)
    {
        $this->defaultRole = $role;
        return $this;
    }

    /**
     * Define role hierarchy
     *
     * @access public
     * @param  string $role
     * @param  array  $subroles
     * @return AccessMap
     */
    public function setRoleHierarchy($role, array $subroles)
    {
        foreach ($subroles as $subrole) {
            if (isset($this->hierarchy[$subrole])) {
                $this->hierarchy[$subrole][] = $role;
            } else {
                $this->hierarchy[$subrole] = array($role);
            }
        }

        return $this;
    }

    /**
     * Get computed role hierarchy
     *
     * @access public
     * @param  string  $role
     * @return array
     */
    public function getRoleHierarchy($role)
    {
        $roles = array($role);

        if (isset($this->hierarchy[$role])) {
            $roles = array_merge($roles, $this->hierarchy[$role]);
        }

        return $roles;
    }

    /**
     * Get the highest role from a list
     *
     * @access public
     * @param  array  $roles
     * @return string
     */
    public function getHighestRole(array $roles)
    {
        $rank = array();

        foreach ($roles as $role) {
            $rank[$role] = count($this->getRoleHierarchy($role));
        }

        asort($rank);

        return key($rank);
    }

    /**
     * Add new access rules
     *
     * @access public
     * @param  string $controller  Controller class name
     * @param  mixed  $methods     List of method name or just one method
     * @param  string $role        Lowest role required
     * @return AccessMap
     */
    public function add($controller, $methods, $role)
    {
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $this->addRule($controller, $method, $role);
            }
        } else {
            $this->addRule($controller, $methods, $role);
        }

        return $this;
    }

    /**
     * Add new access rule
     *
     * @access private
     * @param  string $controller
     * @param  string $method
     * @param  string $role
     * @return AccessMap
     */
    private function addRule($controller, $method, $role)
    {
        $controller = strtolower($controller);
        $method = strtolower($method);

        if (! isset($this->map[$controller])) {
            $this->map[$controller] = array();
        }

        $this->map[$controller][$method] = $role;

        return $this;
    }

    /**
     * Get roles that match the given controller/method
     *
     * @access public
     * @param  string $controller
     * @param  string $method
     * @return array
     */
    public function getRoles($controller, $method)
    {
        $controller = strtolower($controller);
        $method = strtolower($method);

        foreach (array($method, '*') as $key) {
            if (isset($this->map[$controller][$key])) {
                return $this->getRoleHierarchy($this->map[$controller][$key]);
            }
        }

        return $this->getRoleHierarchy($this->defaultRole);
    }
}
