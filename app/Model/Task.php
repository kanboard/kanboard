<?php

namespace Model;

/**
 * Task model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Task extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE               = 'tasks';

    /**
     * Task status
     *
     * @var integer
     */
    const STATUS_OPEN         = 1;
    const STATUS_CLOSED       = 0;

    /**
     * Events
     *
     * @var string
     */
    const EVENT_MOVE_COLUMN     = 'task.move.column';
    const EVENT_MOVE_POSITION   = 'task.move.position';
    const EVENT_UPDATE          = 'task.update';
    const EVENT_CREATE          = 'task.create';
    const EVENT_CLOSE           = 'task.close';
    const EVENT_OPEN            = 'task.open';
    const EVENT_CREATE_UPDATE   = 'task.create_update';
    const EVENT_ASSIGNEE_CHANGE = 'task.assignee_change';

    /**
     * Prepare data before task creation or modification
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
    }

    /**
     * Prepare data before task creation
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepareCreation(array &$values)
    {
        $this->prepare($values);

        if (empty($values['column_id'])) {
            $values['column_id'] = $this->board->getFirstColumn($values['project_id']);
        }

        if (empty($values['color_id'])) {
            $colors = $this->color->getList();
            $values['color_id'] = key($colors);
        }

        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['position'] = $this->taskFinder->countByColumnId($values['project_id'], $values['column_id']) + 1;
    }

    /**
     * Prepare data before task modification
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepareModification(array &$values)
    {
        $this->prepare($values);
        $values['date_modification'] = time();
    }

    /**
     * Create a task
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean|integer
     */
    public function create(array $values)
    {
        $this->db->startTransaction();

        $this->prepareCreation($values);

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE_UPDATE, array('task_id' => $task_id) + $values);
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $values);

        return $task_id;
    }

    /**
     * Update a task
     *
     * @access public
     * @param  array    $values            Form values
     * @param  boolean  $trigger_Events    Trigger events
     * @return boolean
     */
    public function update(array $values, $trigger_events = true)
    {
        // Fetch original task
        $original_task = $this->taskFinder->getById($values['id']);

        if (! $original_task) {
            return false;
        }

        // Prepare data
        $updated_task = $values;
        $this->prepareModification($updated_task);

        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->update($updated_task);

        if ($result && $trigger_events) {
            $this->triggerUpdateEvents($original_task, $updated_task);
        }

        return true;
    }

    /**
     * Trigger events for task modification
     *
     * @access public
     * @param  array    $original_task    Original task data
     * @param  array    $updated_task     Updated task data
     */
    public function triggerUpdateEvents(array $original_task, array $updated_task)
    {
        $events = array();

        if (isset($updated_task['owner_id']) && $original_task['owner_id'] != $updated_task['owner_id']) {
            $events[] = self::EVENT_ASSIGNEE_CHANGE;
        }
        else if (isset($updated_task['column_id']) && $original_task['column_id'] != $updated_task['column_id']) {
            $events[] = self::EVENT_MOVE_COLUMN;
        }
        else if (isset($updated_task['position']) && $original_task['position'] != $updated_task['position']) {
            $events[] = self::EVENT_MOVE_POSITION;
        }
        else {
            $events[] = self::EVENT_CREATE_UPDATE;
            $events[] = self::EVENT_UPDATE;
        }

        $event_data = array_merge($original_task, $updated_task);
        $event_data['task_id'] = $original_task['id'];

        foreach ($events as $event) {
            $this->event->trigger($event, $event_data);
        }
    }

    /**
     * Mark a task closed
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function close($task_id)
    {
        if (! $this->taskFinder->exists($task_id)) {
            return false;
        }

        $result = $this->db
                        ->table(self::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => 0,
                            'date_completed' => time()
                        ));

        if ($result) {
            $this->event->trigger(self::EVENT_CLOSE, array('task_id' => $task_id) + $this->taskFinder->getById($task_id));
        }

        return $result;
    }

    /**
     * Mark a task open
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function open($task_id)
    {
        if (! $this->taskFinder->exists($task_id)) {
            return false;
        }

        $result = $this->db
                        ->table(self::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => 1,
                            'date_completed' => 0
                        ));

        if ($result) {
            $this->event->trigger(self::EVENT_OPEN, array('task_id' => $task_id) + $this->taskFinder->getById($task_id));
        }

        return $result;
    }

    /**
     * Remove a task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function remove($task_id)
    {
        if (! $this->taskFinder->exists($task_id)) {
            return false;
        }

        $this->file->removeAll($task_id);

        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

    /**
     * Move a task to another column or to another position
     *
     * @access public
     * @param  integer    $project_id        Project id
     * @param  integer    $task_id           Task id
     * @param  integer    $column_id         Column id
     * @param  integer    $position          Position (must be >= 1)
     * @return boolean
     */
    public function movePosition($project_id, $task_id, $column_id, $position)
    {
        // The position can't be lower than 1
        if ($position < 1) {
            return false;
        }

        $board = $this->db->table(Board::TABLE)->eq('project_id', $project_id)->asc('position')->findAllByColumn('id');
        $columns = array();

        // Prepare the columns
        foreach ($board as $board_column_id) {

            $columns[$board_column_id] = $this->db->table(self::TABLE)
                          ->eq('is_active', 1)
                          ->eq('project_id', $project_id)
                          ->eq('column_id', $board_column_id)
                          ->neq('id', $task_id)
                          ->asc('position')
                          ->findAllByColumn('id');
        }

        // The column must exists
        if (! isset($columns[$column_id])) {
            return false;
        }

        // We put our task to the new position
        array_splice($columns[$column_id], $position - 1, 0, $task_id); // print_r($columns);

        // We save the new positions for all tasks
        return $this->savePositions($task_id, $columns);
    }

    /**
     * Save task positions
     *
     * @access private
     * @param  integer     $moved_task_id    Id of the moved task
     * @param  array       $columns          Sorted tasks
     * @return boolean
     */
    private function savePositions($moved_task_id, array $columns)
    {
        $this->db->startTransaction();

        foreach ($columns as $column_id => $column) {

            $position = 1;

            foreach ($column as $task_id) {

                if ($task_id == $moved_task_id) {

                    // Events will be triggered only for that task
                    $result = $this->update(array(
                        'id' => $task_id,
                        'position' => $position,
                        'column_id' => $column_id
                    ));
                }
                else {
                    $result = $this->db->table(self::TABLE)->eq('id', $task_id)->update(array(
                        'position' => $position,
                        'column_id' => $column_id
                    ));
                }

                $position++;

                if (! $result) {
                    $this->db->cancelTransaction();
                    return false;
                }
            }
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Move a task to another project
     *
     * @access public
     * @param  integer    $project_id           Project id
     * @param  array      $task                 Task data
     * @return boolean
     */
    public function moveToAnotherProject($project_id, array $task)
    {
        $values = array();

        // Clear values (categories are different for each project)
        $values['category_id'] = 0;
        $values['owner_id'] = 0;

        // Check if the assigned user is allowed for the new project
        if ($task['owner_id'] && $this->projectPermission->isUserAllowed($project_id, $task['owner_id'])) {
            $values['owner_id'] = $task['owner_id'];
        }

        // We use the first column of the new project
        $values['column_id'] = $this->board->getFirstColumn($project_id);
        $values['position'] = $this->taskFinder->countByColumnId($project_id, $values['column_id']) + 1;
        $values['project_id'] = $project_id;

        // The task will be open (close event binding)
        $values['is_active'] = 1;

        if ($this->db->table(self::TABLE)->eq('id', $task['id'])->update($values)) {
            return $task['id'];
        }

        return false;
    }

    /**
     * Generic method to duplicate a task
     *
     * @access public
     * @param  array      $task         Task data
     * @param  array      $override     Task properties to override
     * @return integer|boolean
     */
    public function copy(array $task, array $override = array())
    {
        // Values to override
        if (! empty($override)) {
            $task = $override + $task;
        }

        $this->db->startTransaction();

        // Assign new values
        $values = array();
        $values['title'] = $task['title'];
        $values['description'] = $task['description'];
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['date_due'] = $task['date_due'];
        $values['color_id'] = $task['color_id'];
        $values['project_id'] = $task['project_id'];
        $values['column_id'] = $task['column_id'];
        $values['owner_id'] = 0;
        $values['creator_id'] = $task['creator_id'];
        $values['position'] = $this->taskFinder->countByColumnId($values['project_id'], $values['column_id']) + 1;
        $values['score'] = $task['score'];
        $values['category_id'] = 0;

        // Check if the assigned user is allowed for the new project
        if ($task['owner_id'] && $this->projectPermission->isUserAllowed($values['project_id'], $task['owner_id'])) {
            $values['owner_id'] = $task['owner_id'];
        }

        // Check if the category exists
        if ($task['category_id'] && $this->category->exists($task['category_id'], $task['project_id'])) {
            $values['category_id'] = $task['category_id'];
        }

        // Save task
        if (! $this->db->table(Task::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        // Duplicate subtasks
        if (! $this->subTask->duplicate($task['id'], $task_id)) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(Task::EVENT_CREATE_UPDATE, array('task_id' => $task_id) + $values);
        $this->event->trigger(Task::EVENT_CREATE, array('task_id' => $task_id) + $values);

        return $task_id;
    }

    /**
     * Duplicate a task to the same project
     *
     * @access public
     * @param  array      $task         Task data
     * @return integer|boolean
     */
    public function duplicateToSameProject($task)
    {
        return $this->copy($task);
    }

    /**
     * Duplicate a task to another project (always copy to the first column)
     *
     * @access public
     * @param  integer    $project_id   Destination project id
     * @param  array      $task         Task data
     * @return integer|boolean
     */
    public function duplicateToAnotherProject($project_id, array $task)
    {
        return $this->copy($task, array(
            'project_id' => $project_id,
            'column_id' => $this->board->getFirstColumn($project_id),
        ));
    }

    /**
     * Get a the task id from a text
     *
     * Example: "Fix bug #1234" will return 1234
     *
     * @access public
     * @param  string   $message   Text
     * @return integer
     */
    public function getTaskIdFromText($message)
    {
        if (preg_match('!#(\d+)!i', $message, $matches) && isset($matches[1])) {
            return $matches[1];
        }

        return 0;
    }
}
