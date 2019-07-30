<?php

namespace Kanboard\Controller;

use Kanboard\Core\ExternalTask\ExternalTaskException;

/**
 * External Task Creation Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ExternalTaskCreationController extends BaseController
{
    public function step1(array $values = array(), $errorMessage = '')
    {
        $project = $this->getProject();
        $providerName = $this->request->getStringParam('provider_name');
        $taskProvider = $this->externalTaskManager->getProvider($providerName);

        if (empty($values)) {
            $values = array(
                'swimlane_id' => $this->request->getIntegerParam('swimlane_id'),
                'column_id' => $this->request->getIntegerParam('column_id'),
            );
        }

        $this->response->html($this->template->render('external_task_creation/step1', array(
            'project' => $project,
            'values' => $values,
            'error_message' => $errorMessage,
            'provider_name' => $providerName,
            'template' => $taskProvider->getImportFormTemplate(),
        )));
    }

    public function step2(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $providerName = $this->request->getStringParam('provider_name');

        try {
            $taskProvider = $this->externalTaskManager->getProvider($providerName);

            if (empty($values)) {
                $values = $this->request->getValues();
                $externalTask = $taskProvider->fetch($taskProvider->buildTaskUri($values), $project['id']);

                $values = $externalTask->getFormValues() + array(
                    'external_uri' => $externalTask->getUri(),
                    'external_provider' => $providerName,
                    'project_id' => $project['id'],
                    'swimlane_id' => $values['swimlane_id'],
                    'column_id' => $values['column_id'],
                    'color_id' => $this->colorModel->getDefaultColor(),
                    'owner_id' => $this->userSession->getId(),
                );
            } else {
                $externalTask = $taskProvider->fetch($values['external_uri'], $project['id']);
            }

            $this->response->html($this->template->render('external_task_creation/step2', array(
                'project' => $project,
                'external_task' => $externalTask,
                'provider_name' => $providerName,
                'values' => $values,
                'errors' => $errors,
                'template' => $taskProvider->getCreationFormTemplate(),
                'columns_list' => $this->columnModel->getList($project['id']),
                'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true, false, $project['is_private'] == 1),
                'categories_list' => $this->categoryModel->getList($project['id']),
                'swimlanes_list' => $this->swimlaneModel->getList($project['id'], false, true),
            )));
        } catch (ExternalTaskException $e) {
            $this->step1($values, $e->getMessage());
        }
    }

    public function step3()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateCreation($values);

        if (! $valid) {
            $this->step2($values, $errors);
        } else if (! $this->helper->projectRole->canCreateTaskInColumn($project['id'], $values['column_id'])) {
            $this->flash->failure(t('You cannot create tasks in this column.'));
            $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])), true);
        } else {
            $taskId = $this->taskCreationModel->create($values);
            $this->flash->success(t('Task created successfully.'));
            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $project['id'], 'task_id' => $taskId)), true);
        }
    }
}
