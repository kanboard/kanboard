<?php

namespace Kanboard\Api\Authorization;

use JsonRPC\Exception\AccessDeniedException;
use Kanboard\Core\Base;

/**
 * Class ProcedureAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class ProcedureAuthorization extends Base
{
    private $userSpecificProcedures = array(
        'getMe',
        'getMyDashboard',
        'getMyActivityStream',
        'createMyPrivateProject',
        'getMyProjectsList',
        'getMyProjects',
        'getMyOverdueTasks',
    );

    public function check($procedure)
    {
        if (! $this->userSession->isLogged() && in_array($procedure, $this->userSpecificProcedures)) {
            throw new AccessDeniedException('This procedure is not available with the API credentials');
        }
    }
}
