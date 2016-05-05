<?php

namespace Kanboard\Controller;

/**
 * Board Popover
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class BoardPopover extends Base
{
    /**
     * Confirmation before to close all column tasks
     *
     * @access public
     */
    public function confirmCloseColumnTasks()
    {
        $project = $this->getProject();
        $column_id = $this->request->getIntegerParam('column_id');
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        $this->response->html($this->template->render('board_popover/close_all_tasks_column', array(
            'project' => $project,
            'nb_tasks' => $this->taskFinder->countByColumnAndSwimlaneId($project['id'], $column_id, $swimlane_id),
            'column' => $this->column->getColumnTitleById($column_id),
            'swimlane' => $this->swimlane->getNameById($swimlane_id) ?: t($project['default_swimlane']),
            'values' => array('column_id' => $column_id, 'swimlane_id' => $swimlane_id),
        )));
    }

    /**
     * Close all column tasks
     *
     * @access public
     */
    public function closeColumnTasks()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $this->taskStatus->closeTasksBySwimlaneAndColumn($values['swimlane_id'], $values['column_id']);
        $this->flash->success(t('All tasks of the column "%s" and the swimlane "%s" have been closed successfully.', $this->column->getColumnTitleById($values['column_id']), $this->swimlane->getNameById($values['swimlane_id']) ?: t($project['default_swimlane'])));
        $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $project['id'])));
    }
}
