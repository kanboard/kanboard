<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskIdExclusionFilter;
use Kanboard\Filter\TaskIdFilter;
use Kanboard\Filter\TaskProjectsFilter;
use Kanboard\Filter\TaskStartsWithIdFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Filter\TaskTitleFilter;
use Kanboard\Model\TaskModel;

/**
 * Task Ajax Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskAjaxController extends BaseController
{
    /**
     * Task auto-completion (Ajax)
     *
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $project_ids = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());
        $exclude_task_id = $this->request->getIntegerParam('exclude_task_id');

        if (empty($project_ids)) {
            $this->response->json(array());
        } else {

            $filter = $this->taskQuery->withFilter(new TaskProjectsFilter($project_ids));

            if (! empty($exclude_task_id)) {
                $filter->withFilter(new TaskIdExclusionFilter(array($exclude_task_id)));
            }

            if (ctype_digit((string) $search)) {
                $filter->withFilter(new TaskIdFilter($search));
            } else {
                $filter->withFilter(new TaskTitleFilter($search));
            }

            $this->response->json($filter->format($this->taskAutoCompleteFormatter));
        }
    }

    /**
     * Task ID suggest menu
     */
    public function suggest()
    {
        $taskId = $this->request->getIntegerParam('search');
        $projectIds = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());

        if (empty($projectIds)) {
            $this->response->json(array());
        } else {
            $filter = $this->taskQuery
                ->withFilter(new TaskProjectsFilter($projectIds))
                ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
                ->withFilter(new TaskStartsWithIdFilter($taskId));

            $this->response->json($filter->format($this->taskSuggestMenuFormatter));
        }
    }

    /**
     * Task edit preview
     */
    public function preview()
    {
        $text = $this->request->getRawValue('text');

        if (empty($text)) {
            $this->response->json(array());
        } else {
            $preview = $this->helper->text->markdown($text);
            $this->response->json(array($preview));
        }
    }

}
