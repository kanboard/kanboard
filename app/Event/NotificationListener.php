<?php

namespace Event;

/**
 * Notification listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class NotificationListener extends Base
{
    /**
     * Template name
     *
     * @accesss private
     * @var string
     */
    private $template = '';

    /**
     * Set template name
     *
     * @access public
     * @param  string      $template       Template name
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        $values = $this->getTemplateData($data);
        $users = $this->notification->getUsersList($values['task']['project_id']);

        if ($users) {
            $this->notification->sendEmails($this->template, $users, $values);
            return true;
        }

        return false;
    }

    /**
     * Fetch data for the mail template
     *
     * @access public
     * @param  array    $data    Event data
     * @return array
     */
    public function getTemplateData(array $data)
    {
        $values = array();

        switch ($this->getEventNamespace()) {
            case 'task':
                $values['task'] = $this->taskFinder->getDetails($data['task_id']);
                break;
            case 'subtask':
                $values['subtask'] = $this->subtask->getById($data['id'], true);
                $values['task'] = $this->taskFinder->getDetails($data['task_id']);
                break;
            case 'file':
                $values['file'] = $data;
                $values['task'] = $this->taskFinder->getDetails($data['task_id']);
                break;
            case 'comment':
                $values['comment'] = $this->comment->getById($data['id']);
                $values['task'] = $this->taskFinder->getDetails($values['comment']['task_id']);
                break;
        }

        return $values;
    }
}
