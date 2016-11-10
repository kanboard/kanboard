<?php

namespace Kanboard\Middleware;

use Kanboard\Core\Controller\BaseMiddleware;

/**
 * Class BootstrapMiddleware
 *
 * @package Kanboard\Middleware
 * @author  Frederic Guillot
 */
class BootstrapMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware
     */
    public function execute()
    {
        $this->sessionManager->open();
        $this->dispatcher->dispatch('app.bootstrap');
        $this->sendHeaders();
        $this->next();
    }

    /**
     * Send HTTP headers
     *
     * @access private
     */
    private function sendHeaders()
    {
        $this->response->withContentSecurityPolicy($this->container['cspRules']);
        $this->response->withSecurityHeaders();
        $this->response->withP3P();

        if (ENABLE_XFRAME) {
            $this->response->withXframe();
        }

        if (ENABLE_HSTS) {
            $this->response->withStrictTransportSecurity();
        }
    }
}
