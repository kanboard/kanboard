<?php

namespace Kanboard\Controller;

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
        $this->checkWebhookToken();

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

        list($valid, ) = $this->taskValidator->validateCreation($values);

        if ($valid && $this->taskCreation->create($values)) {
            $this->response->text('OK');
        }

        $this->response->text('FAILED');
    }
}
