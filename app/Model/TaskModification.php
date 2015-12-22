<?php

namespace Kanboard\Model;

use Kanboard\Event\TaskEvent;

/**
 * Task Modification
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskModification extends Base
{
    /**
     * Update a task
     *
     * @access public
     * @param  array     $values
     * @return boolean
     */
    public function update(array $values)
    {
        $original_task = $this->taskFinder->getById($values['id']);

        if(array_key_exists('opposite_task_id', $values) &&
            is_numeric($values['opposite_task_id'])) {

            $parent_task = $this->taskLink->getParentTask($values['task_id']);

            /**
             * If this task is child task update current values otherwise create new relation
             */
            if(empty($parent_task)) {
                if(!$this->taskLink->create($values['id'], $values['opposite_task_id'], TaskLink::RELATION_IS_A_PARENT_OF)) {
                    return false;
                }
            } else {
                if(!$this->taskLink->update($values['task_link_id'], $values['id'], $values['opposite_task_id'], TaskLink::RELATION_IS_A_PARENT_OF)) {
                    return false;
                }
            }
        }

        $this->prepare($values);
        $result = $this->db->table(Task::TABLE)->eq('id', $original_task['id'])->update($values);

        if ($result) {
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
            $events[] = Task::EVENT_ASSIGNEE_CHANGE;
        } else {
            $events[] = Task::EVENT_CREATE_UPDATE;
            $events[] = Task::EVENT_UPDATE;
        }

        foreach ($events as $event) {
            $this->container['dispatcher']->dispatch($event, new TaskEvent($event_data));
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
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $this->dateParser->convert($values, array('date_due'));
        $this->dateParser->convert($values, array('date_started'), true);
        $this->removeFields($values, array('another_task', 'id', 'task_link_id', 'task_id', 'opposite_task_id', 'link_id', 'parent_title'));
        $this->resetFields($values, array('date_due', 'date_started', 'score', 'category_id', 'time_estimated', 'time_spent'));
        $this->convertIntegerFields($values, array('is_active', 'recurrence_status', 'recurrence_trigger', 'recurrence_factor', 'recurrence_timeframe', 'recurrence_basedate'));

        $values['date_modification'] = time();
    }
}
