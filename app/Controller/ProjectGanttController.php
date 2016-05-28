<?php

namespace Kanboard\Controller;

use Kanboard\Filter\ProjectIdsFilter;
use Kanboard\Filter\ProjectStatusFilter;
use Kanboard\Filter\ProjectTypeFilter;
use Kanboard\Formatter\ProjectGanttFormatter;
use Kanboard\Model\ProjectModel;

/**
 * Projects Gantt Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectGanttController extends BaseController
{
    /**
     * Show Gantt chart for all projects
     */
    public function show()
    {
        $project_ids = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());
        $filter = $this->projectQuery
            ->withFilter(new ProjectTypeFilter(ProjectModel::TYPE_TEAM))
            ->withFilter(new ProjectStatusFilter(ProjectModel::ACTIVE))
            ->withFilter(new ProjectIdsFilter($project_ids));

        $filter->getQuery()->asc(ProjectModel::TABLE.'.start_date');

        $this->response->html($this->helper->layout->app('project_gantt/show', array(
            'projects' => $filter->format(new ProjectGanttFormatter($this->container)),
            'title' => t('Gantt chart for all projects'),
        )));
    }

    /**
     * Save new project start date and end date
     */
    public function save()
    {
        $values = $this->request->getJson();

        $result = $this->projectModel->update(array(
            'id' => $values['id'],
            'start_date' => $this->dateParser->getIsoDate(strtotime($values['start'])),
            'end_date' => $this->dateParser->getIsoDate(strtotime($values['end'])),
        ));

        if (! $result) {
            $this->response->json(array('message' => 'Unable to save project'), 400);
        } else {
            $this->response->json(array('message' => 'OK'), 201);
        }
    }
}
