<?php

namespace Controller;

/**
 * Webhook controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Webhook extends Base
{
    /**
     * Webhook to create a task
     *
     * @access public
     */
    public function task()
    {
        if ($this->config->get('webhook_token') !== $this->request->getStringParam('token')) {
            $this->response->text('Not Authorized', 401);
        }

        $defaultProject = $this->project->getFirst();

        $values = array(
            'title' => $this->request->getStringParam('title'),
            'description' => $this->request->getStringParam('description'),
            'color_id' => $this->request->getStringParam('color_id'),
            'project_id' => $this->request->getIntegerParam('project_id', $defaultProject['id']),
            'owner_id' => $this->request->getIntegerParam('owner_id'),
            'column_id' => $this->request->getIntegerParam('column_id'),
            'category_id' => $this->request->getIntegerParam('category_id'),
        );

        list($valid,) = $this->taskValidator->validateCreation($values);

        if ($valid && $this->taskCreation->create($values)) {
            $this->response->text('OK');
        }

        $this->response->text('FAILED');
    }

    /**
     * Handle Github webhooks
     *
     * @access public
     */
    public function github()
    {
        if ($this->config->get('webhook_token') !== $this->request->getStringParam('token')) {
            $this->response->text('Not Authorized', 401);
        }

        $this->githubWebhook->setProjectId($this->request->getIntegerParam('project_id'));

        $result = $this->githubWebhook->parsePayload(
            $this->request->getHeader('X-Github-Event'),
            $this->request->getJson()
        );

        echo $result ? 'PARSED' : 'IGNORED';
    }
}
