<?php

namespace Kanboard\Core\Security;

/**
 * Authorization Handler
 *
 * @package  security
 * @author   Frederic Guillot
 */
class Authorization
{
    /**
     * Access Map
     *
     * @access private
     * @var AccessMap
     */
    private $accessMap;

    /**
     * Constructor
     *
     * @access public
     * @param  AccessMap  $accessMap
     */
    public function __construct(AccessMap $accessMap)
    {
        $this->accessMap = $accessMap;
    }

    /**
     * Check if the given role is allowed to access to the specified resource
     *
     * @access public
     * @param  string  $controller
     * @param  string  $method
     * @param  string  $role
     * @return boolean
     */
    public function isAllowed($controller, $method, $role)
    {
        $roles = $this->accessMap->getRoles($controller, $method);
        return in_array($role, $roles);
    }
}
