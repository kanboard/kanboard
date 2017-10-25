<?php

namespace JsonRPC;

use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\AuthenticationFailureException;

/**
 * Interface MiddlewareInterface
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
interface MiddlewareInterface
{
    /**
     * Execute Middleware
     *
     * @access public
     * @param  string $username
     * @param  string $password
     * @param  string $procedureName
     * @throws AccessDeniedException
     * @throws AuthenticationFailureException
     */
    public function execute($username, $password, $procedureName);
}
