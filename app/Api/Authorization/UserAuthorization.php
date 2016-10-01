<?php

namespace Kanboard\Api\Authorization;

use JsonRPC\Exception\AccessDeniedException;
use Kanboard\Core\Base;

/**
 * Class UserAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class UserAuthorization extends Base
{
    public function check($class, $method)
    {
        if ($this->userSession->isLogged() && ! $this->apiAuthorization->isAllowed($class, $method, $this->userSession->getRole())) {
            throw new AccessDeniedException('You are not allowed to access to this resource');
        }
    }
}
