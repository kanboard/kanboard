<?php

namespace Kanboard\Middleware;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\BaseMiddleware;

/**
 * Class ApplicationAuthorizationMiddleware
 *
 * @package Kanboard\Middleware
 * @author  Frederic Guillot
 */
class ApplicationAuthorizationMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware
     */
    public function execute()
    {
        if (! $this->helper->user->hasAccess($this->router->getController(), $this->router->getAction())) {
            throw new AccessForbiddenException();
        }

        $this->next();
    }
}
