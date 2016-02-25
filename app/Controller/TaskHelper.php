<?php

namespace Kanboard\Controller;

/**
 * Task Ajax Helper
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class TaskHelper extends Base
{
    /**
     * Render Markdown text and reply with the HTML Code
     *
     * @access public
     */
    public function preview()
    {
        $payload = $this->request->getJson();

        if (empty($payload['text'])) {
            $this->response->html('<p>'.t('Nothing to preview...').'</p>');
        }

        $this->response->html($this->helper->text->markdown($payload['text']));
    }

    /**
     * Task autocompletion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $projects = $this->projectPermission->getActiveProjectIds($this->userSession->getId());

        if (empty($projects)) {
            $this->response->json(array());
        }

        $filter = $this->taskFilterAutoCompleteFormatter
            ->create()
            ->filterByProjects($projects)
            ->excludeTasks(array($this->request->getIntegerParam('exclude_task_id')));

        // Search by task id or by title
        if (ctype_digit($search)) {
            $filter->filterById($search);
        } else {
            $filter->filterByTitle($search);
        }

        $this->response->json($filter->format());
    }
}
