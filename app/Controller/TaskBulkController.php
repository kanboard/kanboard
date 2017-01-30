<?php

namespace Kanboard\Controller;

/**
 * Class TaskBulkController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class TaskBulkController extends BaseController
{
    /**
     * Show the form
     *
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values = array(
                'swimlane_id' => $this->request->getIntegerParam('swimlane_id'),
                'column_id' => $this->request->getIntegerParam('column_id'),
                'project_id' => $project['id'],
            );
        }

        $this->response->html($this->template->render('task_bulk/show', array(
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true, false, $project['is_private'] == 1),
            'colors_list' => $this->colorModel->getList(),
            'categories_list' => $this->categoryModel->getList($project['id']),
        )));
    }

    /**
     * Save all tasks in the database
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        list($valid, $errors) = $this->taskValidator->validateBulkCreation($values);

        if (! $valid) {
            $this->show($values, $errors);
        } else if (! $this->helper->projectRole->canCreateTaskInColumn($project['id'], $values['column_id'])) {
            $this->flash->failure(t('You cannot create tasks in this column.'));
            $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])), true);
        } else {
            $this->createTasks($project, $values);
            $this->response->redirect($this->helper->url->to(
                'BoardViewController',
                'show',
                array('project_id' => $project['id']),
                'swimlane-'. $values['swimlane_id']
            ), true);
        }
    }

    /**
     * Create all tasks
     *
     * @param array $project
     * @param array $values
     */
    protected function createTasks(array $project, array $values)
    {
        $tasks = preg_split('/\r\n|[\r\n]/', $values['tasks']);

        foreach ($tasks as $title) {
            $title = trim($title);

            if (! empty($title)) {
                $this->taskCreationModel->create(array(
                    'title' => $title,
                    'column_id' => $values['column_id'],
                    'swimlane_id' => $values['swimlane_id'],
                    'category_id' => empty($values['category_id']) ? 0 : $values['category_id'],
                    'owner_id' => empty($values['owner_id']) ? 0 : $values['owner_id'],
                    'color_id' => $values['color_id'],
                    'project_id' => $project['id'],
                ));
            }
        }
    }
}
