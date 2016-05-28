<?php

namespace Kanboard\Middleware;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\BaseMiddleware;

/**
 * Class ProjectAuthorizationMiddleware
 *
 * @package Kanboard\Middleware
 * @author  Frederic Guillot
 */
class ProjectAuthorizationMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware
     */
    public function execute()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task_id = $this->request->getIntegerParam('task_id');

        if ($task_id > 0 && $project_id === 0) {
            $project_id = $this->taskFinderModel->getProjectId($task_id);
        }

        if ($project_id > 0 && ! $this->helper->user->hasProjectAccess($this->router->getController(), $this->router->getAction(), $project_id)) {
            throw new AccessForbiddenException();
        }

        $this->next();
    }
}
