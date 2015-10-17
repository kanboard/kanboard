<?php

namespace Kanboard\Controller;

/**
 * Task Creation controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Taskcreation extends Base
{
    /**
     * Display a form to create a new task
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $method = $this->request->isAjax() ? 'render' : 'layout';
        $swimlanes_list = $this->swimlane->getList($project['id'], false, true);

        if (empty($values)) {
            $values = array(
                'swimlane_id' => $this->request->getIntegerParam('swimlane_id', key($swimlanes_list)),
                'column_id' => $this->request->getIntegerParam('column_id'),
                'color_id' => $this->request->getStringParam('color_id', $this->color->getDefaultColor()),
                'owner_id' => $this->request->getIntegerParam('owner_id'),
                'another_task' => $this->request->getIntegerParam('another_task'),
            );
        }

        $this->response->html($this->template->$method('task_creation/form', array(
            'ajax' => $this->request->isAjax(),
            'errors' => $errors,
            'values' => $values + array('project_id' => $project['id']),
            'columns_list' => $this->board->getColumnsList($project['id']),
            'users_list' => $this->projectPermission->getMemberList($project['id'], true, false, true),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'swimlanes_list' => $swimlanes_list,
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => $project['name'].' &gt; '.t('New task')
        )));
    }

    /**
     * Validate and save a new task
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateCreation($values);

        if ($valid && $this->taskCreation->create($values)) {
            $this->session->flash(t('Task created successfully.'));
            $this->afterSave($project, $values);
        } else {
            $this->session->flashError(t('Unable to create your task.'));
        }

        $this->create($values, $errors);
    }

    private function afterSave(array $project, array &$values)
    {
        if (isset($values['another_task']) && $values['another_task'] == 1) {
            unset($values['title']);
            unset($values['description']);

            if (! $this->request->isAjax()) {
                $this->response->redirect($this->helper->url->to('taskcreation', 'create', $values));
            }
        } else {
            $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $project['id'])));
        }
    }
}
