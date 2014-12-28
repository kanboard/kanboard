<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Swimlanes
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Swimlane extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'swimlanes';

    /**
     * Value for active swimlanes
     *
     * @var integer
     */
    const ACTIVE = 1;

    /**
     * Value for inactive swimlanes
     *
     * @var integer
     */
    const INACTIVE = 0;

    /**
     * Get a swimlane by the id
     *
     * @access public
     * @param  integer   $swimlane_id    Swimlane id
     * @return array
     */
    public function getById($swimlane_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $swimlane_id)->findOne();
    }

    /**
     * Get the swimlane name by the id
     *
     * @access public
     * @param  integer   $swimlane_id    Swimlane id
     * @return string
     */
    public function getNameById($swimlane_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $swimlane_id)->findOneColumn('name') ?: '';
    }

    /**
     * Get a swimlane id by the project and the name
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @param  string    $name            Name
     * @return integer
     */
    public function getIdByName($project_id, $name)
    {
        return (int) $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('name', $name)
                        ->findOneColumn('id');
    }

    /**
     * Get default swimlane properties
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getDefault($project_id)
    {
        return $this->db->table(Project::TABLE)
                        ->eq('id', $project_id)
                        ->columns('id', 'default_swimlane', 'show_default_swimlane')
                        ->findOne();
    }

    /**
     * Get all swimlanes for a given project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->orderBy('position', 'asc')
                        ->findAll();
    }

    /**
     * Get the list of swimlanes by status
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $status        Status
     * @return array
     */
    public function getAllByStatus($project_id, $status = self::ACTIVE)
    {
        $query = $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('is_active', $status);

        if ($status == self::ACTIVE) {
            $query->asc('position');
        }
        else {
            $query->asc('name');
        }

        return $query->findAll();
    }

    /**
     * Get active swimlanes
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getSwimlanes($project_id)
    {
        $swimlanes = $this->db->table(self::TABLE)
                              ->columns('id', 'name')
                              ->eq('project_id', $project_id)
                              ->eq('is_active', self::ACTIVE)
                              ->orderBy('position', 'asc')
                              ->findAll();

        $default_swimlane = $this->db->table(Project::TABLE)
                                     ->eq('id', $project_id)
                                     ->eq('show_default_swimlane', 1)
                                     ->findOneColumn('default_swimlane');

        if ($default_swimlane) {
            array_unshift($swimlanes, array('id' => 0, 'name' => $default_swimlane));
        }

        return $swimlanes;
    }

    /**
     * Get list of all swimlanes
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getSwimlanesList($project_id)
    {
        $swimlanes = $this->db->table(self::TABLE)
                              ->eq('project_id', $project_id)
                              ->orderBy('position', 'asc')
                              ->listing('id', 'name');

        $swimlanes[0] = $this->db->table(Project::TABLE)
                                     ->eq('id', $project_id)
                                     ->findOneColumn('default_swimlane');

        return $swimlanes;
    }

    /**
     * Add a new swimlane
     *
     * @access public
     * @param  integer   $project_id
     * @param  string    $name
     * @return bool
     */
    public function create($project_id, $name)
    {
        return $this->persist(self::TABLE, array(
            'project_id' => $project_id,
            'name' => $name,
            'position' => $this->getLastPosition($project_id),
        ));
    }

    /**
     * Rename a swimlane
     *
     * @access public
     * @param  integer   $swimlane_id    Swimlane id
     * @param  string    $name           Swimlane name
     * @return bool
     */
    public function rename($swimlane_id, $name)
    {
        return $this->db->table(self::TABLE)
                        ->eq('id', $swimlane_id)
                        ->update(array('name' => $name));
    }

    /**
     * Update the default swimlane
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function updateDefault(array $values)
    {
        return $this->db
                    ->table(Project::TABLE)
                    ->eq('id', $values['id'])
                    ->update(array(
                        'default_swimlane' => $values['default_swimlane'],
                        'show_default_swimlane' => $values['show_default_swimlane'],
                    ));
    }

    /**
     * Get the last position of a swimlane
     *
     * @access public
     * @param  integer   $project_id
     * @return integer
     */
    public function getLastPosition($project_id)
    {
        return $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('is_active', 1)
                        ->count() + 1;
    }

    /**
     * Disable a swimlane
     *
     * @access public
     * @param  integer   $project_id     Project id
     * @param  integer   $swimlane_id    Swimlane id
     * @return bool
     */
    public function disable($project_id, $swimlane_id)
    {
        $result = $this->db
                    ->table(self::TABLE)
                    ->eq('id', $swimlane_id)
                    ->update(array(
                        'is_active' => self::INACTIVE,
                        'position' => 0,
                    ));

        if ($result) {
            // Re-order positions
            $this->updatePositions($project_id);
        }

        return $result;
    }

    /**
     * Enable a swimlane
     *
     * @access public
     * @param  integer   $project_id     Project id
     * @param  integer   $swimlane_id    Swimlane id
     * @return bool
     */
    public function enable($project_id, $swimlane_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $swimlane_id)
                    ->update(array(
                        'is_active' => self::ACTIVE,
                        'position' => $this->getLastPosition($project_id),
                    ));
    }

    /**
     * Remove a swimlane
     *
     * @access public
     * @param  integer   $project_id     Project id
     * @param  integer   $swimlane_id    Swimlane id
     * @return bool
     */
    public function remove($project_id, $swimlane_id)
    {
        $this->db->startTransaction();

        // Tasks should not be assigned anymore to this swimlane
        $this->db->table(Task::TABLE)->eq('swimlane_id', $swimlane_id)->update(array('swimlane_id' => 0));

        if (! $this->db->table(self::TABLE)->eq('id', $swimlane_id)->remove()) {
            $this->db->cancelTransaction();
            return false;
        }

        // Re-order positions
        $this->updatePositions($project_id);

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Update swimlane positions after disabling or removing a swimlane
     *
     * @access public
     * @param  integer  $project_id     Project id
     * @return boolean
     */
    public function updatePositions($project_id)
    {
        $position = 0;
        $swimlanes = $this->db->table(self::TABLE)
                              ->eq('project_id', $project_id)
                              ->eq('is_active', 1)
                              ->asc('position')
                              ->findAllByColumn('id');

        if (! $swimlanes) {
            return false;
        }

        foreach ($swimlanes as $swimlane_id) {
            $this->db->table(self::TABLE)
                     ->eq('id', $swimlane_id)
                     ->update(array('position' => ++$position));
        }

        return true;
    }

    /**
     * Move a swimlane down, increment the position value
     *
     * @access public
     * @param  integer  $project_id     Project id
     * @param  integer  $swimlane_id    Swimlane id
     * @return boolean
     */
    public function moveDown($project_id, $swimlane_id)
    {
        $swimlanes = $this->db->table(self::TABLE)
                              ->eq('project_id', $project_id)
                              ->eq('is_active', self::ACTIVE)
                              ->asc('position')
                              ->listing('id', 'position');

        $positions = array_flip($swimlanes);

        if (isset($swimlanes[$swimlane_id]) && $swimlanes[$swimlane_id] < count($swimlanes)) {

            $position = ++$swimlanes[$swimlane_id];
            $swimlanes[$positions[$position]]--;

            $this->db->startTransaction();
            $this->db->table(self::TABLE)->eq('id', $swimlane_id)->update(array('position' => $position));
            $this->db->table(self::TABLE)->eq('id', $positions[$position])->update(array('position' => $swimlanes[$positions[$position]]));
            $this->db->closeTransaction();

            return true;
        }

        return false;
    }

    /**
     * Move a swimlane up, decrement the position value
     *
     * @access public
     * @param  integer  $project_id     Project id
     * @param  integer  $swimlane_id    Swimlane id
     * @return boolean
     */
    public function moveUp($project_id, $swimlane_id)
    {
        $swimlanes = $this->db->table(self::TABLE)
                              ->eq('project_id', $project_id)
                              ->eq('is_active', self::ACTIVE)
                              ->asc('position')
                              ->listing('id', 'position');

        $positions = array_flip($swimlanes);

        if (isset($swimlanes[$swimlane_id]) && $swimlanes[$swimlane_id] > 1) {

            $position = --$swimlanes[$swimlane_id];
            $swimlanes[$positions[$position]]++;

            $this->db->startTransaction();
            $this->db->table(self::TABLE)->eq('id', $swimlane_id)->update(array('position' => $position));
            $this->db->table(self::TABLE)->eq('id', $positions[$position])->update(array('position' => $swimlanes[$positions[$position]]));
            $this->db->closeTransaction();

            return true;
        }

        return false;
    }

    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Required('name', t('The name is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Required('name', t('The name is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate default swimlane modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateDefaultModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Required('default_swimlane', t('The name is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Integer('id', t('The id must be an integer')),
            new Validators\Integer('project_id', t('The project id must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50)
        );
    }
}
