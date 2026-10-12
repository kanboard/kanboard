<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;

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

        if (empty($values['column_id']) || empty($values['swimlane_id'])) {
            $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])));
            return;
        }

        $column_id = (int) $values['column_id'];
        $swimlane_id = (int) $values['swimlane_id'];

        $column = $this->columnModel->getById($column_id);
        $swimlane = $this->swimlaneModel->getById($swimlane_id);

        if (empty($column) || empty($swimlane)) {
            throw new PageNotFoundException();
        }

        if ((int) $column['project_id'] !== (int) $project['id']
            || (int) $swimlane['project_id'] !== (int) $project['id']) {
            throw new AccessForbiddenException();
        }

        if (! $this->helper->user->hasProjectAccess('BoardPopoverController', 'closeColumnTasks', $project['id'])) {
            throw new AccessForbiddenException();
        }

        $this->taskStatusModel->closeTasksBySwimlaneAndColumn($swimlane_id, $column_id);
        $this->flash->success(t('All tasks of the column "%s" and the swimlane "%s" have been closed successfully.', $this->columnModel->getColumnTitleById($column_id), $this->swimlaneModel->getNameById($swimlane_id)));
        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])));
    }
}
