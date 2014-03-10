<?php

namespace Model;

require_once __DIR__.'/base.php';
require_once __DIR__.'/comment.php';

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class Task extends Base
{
    const TABLE               = 'tasks';
    const EVENT_MOVE_COLUMN   = 'task.move.column';
    const EVENT_MOVE_POSITION = 'task.move.position';
    const EVENT_UPDATE        = 'task.update';
    const EVENT_CREATE        = 'task.create';
    const EVENT_CLOSE         = 'task.close';
    const EVENT_OPEN          = 'task.open';

    public function getColors()
    {
        return array(
            'yellow' => t('Yellow'),
            'blue' => t('Blue'),
            'green' => t('Green'),
            'purple' => t('Purple'),
            'red' => t('Red'),
            'orange' => t('Orange'),
            'grey' => t('Grey'),
        );
    }

    public function getById($task_id, $more = false)
    {
        if ($more) {

            return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.title',
                        self::TABLE.'.description',
                        self::TABLE.'.date_creation',
                        self::TABLE.'.date_completed',
                        self::TABLE.'.date_due',
                        self::TABLE.'.color_id',
                        self::TABLE.'.project_id',
                        self::TABLE.'.column_id',
                        self::TABLE.'.owner_id',
                        self::TABLE.'.position',
                        self::TABLE.'.is_active',
                        self::TABLE.'.score',
                        Project::TABLE.'.name AS project_name',
                        Board::TABLE.'.title AS column_title',
                        User::TABLE.'.username'
                    )
                    ->join(Project::TABLE, 'id', 'project_id')
                    ->join(Board::TABLE, 'id', 'column_id')
                    ->join(User::TABLE, 'id', 'owner_id')
                    ->eq(self::TABLE.'.id', $task_id)
                    ->findOne();
        }
        else {

            return $this->db->table(self::TABLE)->eq('id', $task_id)->findOne();
        }
    }

    public function getAllByProjectId($project_id, array $status = array(1, 0))
    {
        return $this->db->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.title',
                self::TABLE.'.description',
                self::TABLE.'.date_creation',
                self::TABLE.'.date_completed',
                self::TABLE.'.date_due',
                self::TABLE.'.color_id',
                self::TABLE.'.project_id',
                self::TABLE.'.column_id',
                self::TABLE.'.owner_id',
                self::TABLE.'.position',
                self::TABLE.'.is_active',
                self::TABLE.'.score',
                Board::TABLE.'.title AS column_title',
                User::TABLE.'.username'
            )
            ->join(Board::TABLE, 'id', 'column_id')
            ->join(User::TABLE, 'id', 'owner_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->in('is_active', $status)
            ->desc('date_completed')
            ->findAll();
    }

    public function countByProjectId($project_id, $status = array(1, 0))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->in('is_active', $status)
                    ->count();
    }

    public function getAllByColumnId($project_id, $column_id, $status = array(1))
    {
        $tasks = $this->db
                    ->table(self::TABLE)
                    ->columns('tasks.id', 'title', 'color_id', 'project_id', 'owner_id', 'column_id', 'position', 'score', 'date_due', 'users.username')
                    ->join('users', 'id', 'owner_id')
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->asc('position')
                    ->findAll();

        $commentModel = new Comment($this->db, $this->event);

        foreach ($tasks as &$task) {
            $task['nb_comments'] = $commentModel->count($task['id']);
        }

        return $tasks;
    }

    public function countByColumnId($project_id, $column_id, $status = array(1))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->count();
    }

    public function duplicateToAnotherProject($task_id, $project_id)
    {
        $this->db->startTransaction();

        $boardModel = new Board($this->db, $this->event);

        // Get the original task
        $task = $this->getById($task_id);

        // Cleanup data
        unset($task['id']);
        unset($task['date_completed']);

        // Assign new values
        $task['date_creation'] = time();
        $task['owner_id'] = 0;
        $task['is_active'] = 1;
        $task['column_id'] = $boardModel->getFirstColumn($project_id);
        $task['project_id'] = $project_id;
        $task['position'] = $this->countByColumnId($task['project_id'], $task['column_id']);

        // Save task
        if (! $this->db->table(self::TABLE)->save($task)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $task);

        return $task_id;
    }

    public function create(array $values)
    {
        $this->db->startTransaction();

        // Prepare data
        if (isset($values['another_task'])) {
            unset($values['another_task']);
        }

        if (! empty($values['date_due']) && ! is_numeric($values['date_due'])) {
            $values['date_due'] = $this->getTimestampFromDate($values['date_due'], t('m/d/Y')) ?: null;
        }

        $values['date_creation'] = time();
        $values['position'] = $this->countByColumnId($values['project_id'], $values['column_id']);

        // Save task
        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $values);

        return $task_id;
    }

    public function update(array $values)
    {
        // Prepare data
        if (! empty($values['date_due']) && ! is_numeric($values['date_due'])) {
            $values['date_due'] = $this->getTimestampFromDate($values['date_due'], t('m/d/Y')) ?: null;
        }

        $original_task = $this->getById($values['id']);
        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);

        // Trigger events
        if ($result) {

            $events = array();

            if ($this->event->getLastTriggeredEvent() !== self::EVENT_UPDATE) {
                $events[] = self::EVENT_UPDATE;
            }

            if (isset($values['column_id']) && $original_task['column_id'] != $values['column_id']) {
                $events[] = self::EVENT_MOVE_COLUMN;
            }
            else if (isset($values['position']) && $original_task['position'] != $values['position']) {
                $events[] = self::EVENT_MOVE_POSITION;
            }

            $event_data = array_merge($original_task, $values);
            $event_data['task_id'] = $original_task['id'];

            foreach ($events as $event) {
                $this->event->trigger($event, $event_data);
            }
        }

        return $result;
    }

    // Mark a task closed
    public function close($task_id)
    {
        $result = $this->db
                        ->table(self::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => 0,
                            'date_completed' => time()
                        ));

        if ($result) {
            $this->event->trigger(self::EVENT_CLOSE, array('task_id' => $task_id) + $this->getById($task_id));
        }

        return $result;
    }

    // Mark a task open
    public function open($task_id)
    {
        $result = $this->db
                        ->table(self::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => 1,
                            'date_completed' => ''
                        ));

        if ($result) {
            $this->event->trigger(self::EVENT_OPEN, array('task_id' => $task_id) + $this->getById($task_id));
        }

        return $result;
    }

    // Remove a task
    public function remove($task_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

    // Move a task to another column or to another position
    public function move($task_id, $column_id, $position)
    {
        return $this->update(array(
            'id' => $task_id,
            'column_id' => $column_id,
            'position' => $position,
        ));
    }

    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('color_id', t('The color is required')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('column_id', t('The column is required')),
            new Validators\Integer('column_id', t('This value must be an integer')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
            new Validators\Integer('score', t('This value must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 200), 200),
            new Validators\Date('date_due', t('Invalid date'), t('m/d/Y')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function validateDescriptionCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('description', t('The description is required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('color_id', t('The color is required')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('column_id', t('The column is required')),
            new Validators\Integer('column_id', t('This value must be an integer')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
            new Validators\Integer('score', t('This value must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 200), 200),
            new Validators\Date('date_due', t('Invalid date'), t('m/d/Y')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function validateAssigneeModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('owner_id', t('This value is required')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function getTimestampFromDate($value, $format)
    {
        $date = \DateTime::createFromFormat($format, $value);

        if ($date !== false) {
            $errors = \DateTime::getLastErrors();
            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                $timestamp = $date->getTimestamp();
                return $timestamp > 0 ? $timestamp : 0;
            }
        }

        return 0;
    }
}
