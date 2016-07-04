<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Event\TaskEvent;

/**
 * Task Modification
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskModificationModel extends Base
{
    /**
     * Update a task
     *
     * @access public
     * @param  array     $values
     * @param  boolean   $fire_events
     * @return boolean
     */
    public function update(array $values, $fire_events = true)
    {
        $original_task = $this->taskFinderModel->getById($values['id']);

        $this->updateTags($values, $original_task);
        $this->prepare($values);
        $result = $this->db->table(TaskModel::TABLE)->eq('id', $original_task['id'])->update($values);

        if ($fire_events && $result) {
            $this->fireEvents($original_task, $values);
        }

        return $result;
    }

    /**
     * Fire events
     *
     * @access public
     * @param  array     $task
     * @param  array     $new_values
     */
    public function fireEvents(array $task, array $new_values)
    {
        $events = array();
        $event_data = array_merge($task, $new_values, array('task_id' => $task['id']));

        // Values changed
        $event_data['changes'] = array_diff_assoc($new_values, $task);
        unset($event_data['changes']['date_modification']);

        if ($this->isFieldModified('owner_id', $event_data['changes'])) {
            $events[] = TaskModel::EVENT_ASSIGNEE_CHANGE;
        } elseif (! empty($event_data['changes'])) {
            $events[] = TaskModel::EVENT_CREATE_UPDATE;
            $events[] = TaskModel::EVENT_UPDATE;
        }

        foreach ($events as $event) {
            $this->logger->debug('Event fired: '.$event);
            $this->dispatcher->dispatch($event, new TaskEvent($event_data));
        }
    }

    /**
     * Return true if the field is the only modified value
     *
     * @access public
     * @param  string  $field
     * @param  array   $changes
     * @return boolean
     */
    public function isFieldModified($field, array $changes)
    {
        return isset($changes[$field]) && count($changes) === 1;
    }

    /**
     * Prepare data before task modification
     *
     * @access protected
     * @param  array  $values
     */
    protected function prepare(array &$values)
    {
        $values = $this->dateParser->convert($values, array('date_due'));
        $values = $this->dateParser->convert($values, array('date_started'), true);

        $this->helper->model->removeFields($values, array('another_task', 'id'));
        $this->helper->model->resetFields($values, array('date_due', 'date_started', 'score', 'category_id', 'time_estimated', 'time_spent'));
        $this->helper->model->convertIntegerFields($values, array('priority', 'is_active', 'recurrence_status', 'recurrence_trigger', 'recurrence_factor', 'recurrence_timeframe', 'recurrence_basedate'));

        $values['date_modification'] = time();
    }

    /**
     * Update tags
     *
     * @access protected
     * @param  array  $values
     * @param  array  $original_task
     */
    protected function updateTags(array &$values, array $original_task)
    {
        if (isset($values['tags'])) {
            $this->taskTagModel->save($original_task['project_id'], $values['id'], $values['tags']);
            unset($values['tags']);
        }
    }
}
