<?php

namespace Model;

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
     * @param  boolean   $fire_events
     * @return boolean
     */
    public function update(array $values, $fire_events = true)
    {
        $original_task = $this->taskFinder->getById($values['id']);

        $this->prepare($values);
        $result = $this->db->table(Task::TABLE)->eq('id', $original_task['id'])->update($values);

        if ($result && $fire_events) {
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
        $event_data = array_merge($task, $new_values, array('task_id' => $task['id']));

        if (isset($new_values['owner_id']) && $task['owner_id'] != $new_values['owner_id']) {
            $events = array(Task::EVENT_ASSIGNEE_CHANGE);
        }
        else {
            $events = array(Task::EVENT_CREATE_UPDATE, Task::EVENT_UPDATE);
        }

        foreach ($events as $event) {
            $this->event->trigger($event, $event_data);
        }
    }

    /**
     * Prepare data before task modification
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $this->dateParser->convert($values, array('date_due', 'date_started'));
        $this->removeFields($values, array('another_task', 'id'));
        $this->resetFields($values, array('date_due', 'date_started', 'score', 'category_id', 'time_estimated', 'time_spent'));
        $this->convertIntegerFields($values, array('is_active'));

        $values['date_modification'] = time();
    }
}
