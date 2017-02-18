<?php

namespace Kanboard\Controller;

/**
 * Board Popover Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class BoardPopoverController extends BaseController
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
            'nb_tasks' => $this->taskFinderModel->countByColumnAndSwimlaneId($project['id'], $column_id, $swimlane_id),
            'column' => $this->columnModel->getColumnTitleById($column_id),
            'swimlane' => $this->swimlaneModel->getNameById($swimlane_id),
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

        $this->taskStatusModel->closeTasksBySwimlaneAndColumn($values['swimlane_id'], $values['column_id']);
        $this->flash->success(t('All tasks of the column "%s" and the swimlane "%s" have been closed successfully.', $this->columnModel->getColumnTitleById($values['column_id']), $this->swimlaneModel->getNameById($values['swimlane_id'])));
        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])));
    }
}
