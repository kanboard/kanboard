<?php

namespace Controller;

/**
 * Project Calendar controller
 *
 * @package  controller
 * @author   Frederic Guillot
 * @author   Timo Litzbarski
 */
class Calendar extends Base
{
    /**
     * Show calendar view
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();

        $this->response->html($this->template->layout('calendar/show', array(
            'check_interval' => $this->config->get('board_private_refresh_interval'),
            'users_list' => $this->projectPermission->getMemberList($project['id'], true, true),
            'categories_list' => $this->category->getList($project['id'], true, true),
            'columns_list' => $this->board->getColumnsList($project['id'], true),
            'swimlanes_list' => $this->swimlane->getList($project['id'], true),
            'colors_list' => $this->color->getList(true),
            'status_list' => $this->taskStatus->getList(true),
            'project' => $project,
            'title' => t('Calendar for "%s"', $project['name']),
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
        )));
    }

    /**
     * Get tasks to display on the calendar
     *
     * @access public
     */
    public function events()
    {
        $this->response->json(
            $this->taskFilter
                 ->create()
                 ->filterByProject($this->request->getIntegerParam('project_id'))
                 ->filterByCategory($this->request->getIntegerParam('category_id', -1))
                 ->filterByOwner($this->request->getIntegerParam('owner_id', -1))
                 ->filterByColumn($this->request->getIntegerParam('column_id', -1))
                 ->filterBySwimlane($this->request->getIntegerParam('swimlane_id', -1))
                 ->filterByColor($this->request->getStringParam('color_id'))
                 ->filterByStatus($this->request->getIntegerParam('is_active', -1))
                 ->filterByDueDateRange(
                    $this->request->getStringParam('start'),
                    $this->request->getStringParam('end')
                 )
                 ->toCalendarEvents()
        );
    }

    /**
     * Update task due date
     *
     * @access public
     */
    public function save()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {

            $values = $this->request->getJson();

            $this->taskModification->update(array(
                'id' => $values['task_id'],
                'date_due' => $values['date_due'],
            ));
        }
    }
}
