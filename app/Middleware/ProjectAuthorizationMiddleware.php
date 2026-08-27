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

        // The project of the task always prevails over the project given in the URL
        if ($task_id > 0) {
            $task_project_id = $this->taskFinderModel->getProjectId($task_id);

            if ($task_project_id > 0 && $task_project_id != $project_id) {
                $this->checkProjectAccess($task_project_id);
            }
        }

        if ($project_id > 0) {
            $this->checkProjectAccess($project_id);
        }

        $this->next();
    }

    /**
     * Check the access of the current user for the given project
     *
     * @access protected
     * @param  integer $project_id
     * @throws AccessForbiddenException
     */
    protected function checkProjectAccess($project_id)
    {
        if (! $this->helper->user->hasProjectAccess($this->router->getController(), $this->router->getAction(), $project_id)) {
            throw new AccessForbiddenException();
        }
    }
}
