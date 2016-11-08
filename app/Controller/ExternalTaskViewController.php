<?php

namespace Kanboard\Controller;

use Kanboard\Core\ExternalTask\ExternalTaskException;

/**
 * Class ExternalTaskViewController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ExternalTaskViewController extends BaseController
{
    public function show()
    {
        try {
            $task = $this->getTask();
            $taskProvider = $this->externalTaskManager->getProvider($task['external_provider']);
            $externalTask = $taskProvider->fetch($task['external_uri']);

            $this->response->html($this->template->render($taskProvider->getViewTemplate(), array(
                'task' => $task,
                'external_task' => $externalTask,
            )));
        } catch (ExternalTaskException $e) {
            $this->response->html('<div class="alert alert-error">'.$e->getMessage().'</div>');
        }
    }
}
