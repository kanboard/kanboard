<?php

namespace Kanboard\Api\Authorization;

use JsonRPC\Exception\AccessDeniedException;
use Kanboard\Core\Base;

/**
 * Class ProjectAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class ProjectAuthorization extends Base
{
    public function check($class, $method, $project_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $project_id);
        }
    }
    
    protected function checkProjectPermission($class, $method, $project_id)
    {
        if (empty($project_id)) {
            throw new AccessDeniedException('Project Not Found');
        }
        
        $role = $this->projectUserRoleModel->getUserRole($project_id, $this->userSession->getId());

        if (! $this->apiProjectAuthorization->isAllowed($class, $method, $role)) {
            throw new AccessDeniedException('Project Access Denied');
        }
    }
}
