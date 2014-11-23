<?php

namespace Model;

/**
 * Task Creation
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskCreation extends Base
{
    /**
     * Create a task
     *
     * @access public
     * @param  array    $values   Form values
     * @return integer
     */
    public function create(array $values)
    {
        $this->prepare($values);
        $task_id = $this->persist(Task::TABLE, $values);

        if ($task_id) {
            $this->fireEvents($task_id, $values);
        }

        return (int) $task_id;
    }

    /**
     * Prepare data
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $this->dateParser->convert($values, array('date_due', 'date_started'));
        $this->removeFields($values, array('another_task'));
        $this->resetFields($values, array('owner_id', 'owner_id', 'date_due', 'score', 'category_id', 'time_estimated'));

        if (empty($values['column_id'])) {
            $values['column_id'] = $this->board->getFirstColumn($values['project_id']);
        }

        if (empty($values['color_id'])) {
            $values['color_id'] = $this->color->getDefaultColor();
        }

        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['position'] = $this->taskFinder->countByColumnId($values['project_id'], $values['column_id']) + 1;
    }

    /**
     * Fire events
     *
     * @access private
     * @param  integer  $task_id     Task id
     * @param  array    $values      Form values
     */
    private function fireEvents($task_id, array $values)
    {
        $values['task_id'] = $task_id;
        $this->event->trigger(Task::EVENT_CREATE_UPDATE, $values);
        $this->event->trigger(Task::EVENT_CREATE, $values);
    }
}
