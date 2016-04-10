<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskIdExclusionFilter;
use Kanboard\Filter\TaskIdFilter;
use Kanboard\Filter\TaskProjectsFilter;
use Kanboard\Filter\TaskTitleFilter;
use Kanboard\Formatter\TaskAutoCompleteFormatter;

/**
 * Task Ajax Helper
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class TaskHelper extends Base
{
    /**
     * Task auto-completion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $project_ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());
        $exclude_task_id = $this->request->getIntegerParam('exclude_task_id');

        if (empty($project_ids)) {
            $this->response->json(array());
        } else {

            $filter = $this->taskQuery->withFilter(new TaskProjectsFilter($project_ids));

            if (! empty($exclude_task_id)) {
                $filter->withFilter(new TaskIdExclusionFilter(array($exclude_task_id)));
            }

            if (ctype_digit($search)) {
                $filter->withFilter(new TaskIdFilter($search));
            } else {
                $filter->withFilter(new TaskTitleFilter($search));
            }

            $this->response->json($filter->format(new TaskAutoCompleteFormatter($this->container)));
        }
    }
}
