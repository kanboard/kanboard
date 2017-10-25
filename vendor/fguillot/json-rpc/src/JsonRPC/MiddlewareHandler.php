<?php

namespace JsonRPC;

/**
 * Class MiddlewareHandler
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class MiddlewareHandler
{
    /**
     * Procedure Name
     *
     * @access protected
     * @var string
     */
    protected $procedureName = '';

    /**
     * Username
     *
     * @access protected
     * @var string
     */
    protected $username = '';

    /**
     * Password
     *
     * @access protected
     * @var string
     */
    protected $password = '';

    /**
     * List of middleware to execute before to call the method
     *
     * @access protected
     * @var MiddlewareInterface[]
     */
    protected $middleware = array();

    /**
     * Set username
     *
     * @access public
     * @param  string $username
     * @return $this
     */
    public function withUsername($username)
    {
        if (! empty($username)) {
            $this->username = $username;
        }

        return $this;
    }

    /**
     * Set password
     *
     * @access public
     * @param  string $password
     * @return $this
     */
    public function withPassword($password)
    {
        if (! empty($password)) {
            $this->password = $password;
        }

        return $this;
    }

    /**
     * Set procedure name
     *
     * @access public
     * @param  string $procedureName
     * @return $this
     */
    public function withProcedure($procedureName)
    {
        $this->procedureName = $procedureName;
        return $this;
    }

    /**
     * Add a new middleware
     *
     * @access public
     * @param  MiddlewareInterface $middleware
     * @return MiddlewareHandler
     */
    public function withMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Execute all middleware
     *
     * @access public
     */
    public function execute()
    {
        foreach ($this->middleware as $middleware) {
            $middleware->execute($this->username, $this->password, $this->procedureName);
        }
    }
}
