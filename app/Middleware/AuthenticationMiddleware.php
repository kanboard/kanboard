<?php

namespace Kanboard\Middleware;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\BaseMiddleware;
use Kanboard\Core\Security\Role;

/**
 * Class AuthenticationMiddleware
 *
 * @package Kanboard\Middleware
 * @author  Frederic Guillot
 */
class AuthenticationMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware
     */
    public function execute()
    {
        if (! $this->authenticationManager->checkCurrentSession()) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        if (! $this->isPublicAccess()) {
            $this->handleAuthentication();
        }

        $this->next();
    }

    protected function handleAuthentication()
    {
        if (! $this->userSession->isLogged() && ! $this->authenticationManager->preAuthentication()) {
            $this->nextMiddleware = null;

            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            } else {
                $this->sessionStorage->redirectAfterLogin = $this->request->getUri();
                $this->response->redirect($this->helper->url->to('AuthController', 'login'));
            }
        }
    }

    protected function isPublicAccess()
    {
        if ($this->applicationAuthorization->isAllowed($this->router->getController(), $this->router->getAction(), Role::APP_PUBLIC)) {
            $this->nextMiddleware = null;
            return true;
        }

        return false;
    }
}
