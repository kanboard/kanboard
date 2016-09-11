<?php

namespace Kanboard\Middleware;

use Kanboard\Core\Controller\BaseMiddleware;

/**
 * Class PostAuthenticationMiddleware
 *
 * @package Kanboard\Middleware
 * @author  Frederic Guillot
 */
class PostAuthenticationMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware
     */
    public function execute()
    {
        $controller = strtolower($this->router->getController());
        $action = strtolower($this->router->getAction());
        $ignore = ($controller === 'twofactorcontroller' && in_array($action, array('code', 'check'))) || ($controller === 'authcontroller' && $action === 'logout');

        if ($ignore === false && $this->userSession->hasPostAuthentication() && ! $this->userSession->isPostAuthenticationValidated()) {
            $this->nextMiddleware = null;

            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            } else {
                $this->response->redirect($this->helper->url->to('TwoFactorController', 'code'));
            }
        }

        $this->next();
    }
}
