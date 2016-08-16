<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Swimlanes
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class SwimlaneModel extends Base
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
     * Get a swimlane by the project and the name
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @param  string    $name            Swimlane name
     * @return array
     */
    public function getByName($project_id, $name)
    {
        return $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('name', $name)
                        ->findOne();
    }

    /**
     * Get first active swimlane for a project
     *
     * @access public
     * @param  integer $project_id
     * @return array|null
     */
    public function getFirstActiveSwimlane($project_id)
    {
        $swimlanes = $this->getSwimlanes($project_id);

        if (empty($swimlanes)) {
            return null;
        }

        return $swimlanes[0];
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
        $result = $this->db
            ->table(ProjectModel::TABLE)
            ->eq('id', $project_id)
            ->columns('id', 'default_swimlane', 'show_default_swimlane')
            ->findOne();

        if ($result['default_swimlane'] === 'Default swimlane') {
            $result['default_swimlane'] = t($result['default_swimlane']);
        }

        return $result;
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
        return $this->db
            ->table(self::TABLE)
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
        $query = $this->db
            ->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->eq('is_active', $status);

        if ($status == self::ACTIVE) {
            $query->asc('position');
        } else {
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
        $swimlanes = $this->db
            ->table(self::TABLE)
            ->columns('id', 'name', 'description')
            ->eq('project_id', $project_id)
            ->eq('is_active', self::ACTIVE)
            ->orderBy('position', 'asc')
            ->findAll();

        $defaultSwimlane = $this->db
            ->table(ProjectModel::TABLE)
            ->eq('id', $project_id)
            ->eq('show_default_swimlane', 1)
            ->findOneColumn('default_swimlane');

        if ($defaultSwimlane) {
            if ($defaultSwimlane === 'Default swimlane') {
                $defaultSwimlane = t($defaultSwimlane);
            }

            array_unshift($swimlanes, array('id' => 0, 'name' => $defaultSwimlane));
        }

        return $swimlanes;
    }

    /**
     * Get list of all swimlanes
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  boolean   $prepend       Prepend default value
     * @param  boolean   $only_active   Return only active swimlanes
     * @return array
     */
    public function getList($project_id, $prepend = false, $only_active = false)
    {
        $swimlanes = array();
        $default = $this->db->table(ProjectModel::TABLE)->eq('id', $project_id)->eq('show_default_swimlane', 1)->findOneColumn('default_swimlane');

        if ($prepend) {
            $swimlanes[-1] = t('All swimlanes');
        }

        if (! empty($default)) {
            $swimlanes[0] = $default === 'Default swimlane' ? t($default) : $default;
        }

        return $swimlanes + $this->db
            ->hashtable(self::TABLE)
            ->eq('project_id', $project_id)
            ->in('is_active', $only_active ? array(self::ACTIVE) : array(self::ACTIVE, self::INACTIVE))
            ->orderBy('position', 'asc')
            ->getAll('id', 'name');
    }

    /**
     * Add a new swimlane
     *
     * @access public
     * @param  array    $values   Form values
     * @return integer|boolean
     */
    public function create($values)
    {
        if (! $this->projectModel->exists($values['project_id'])) {
            return 0;
        }

        $values['position'] = $this->getLastPosition($values['project_id']);
        return $this->db->table(self::TABLE)->persist($values);
    }

    /**
     * Update a swimlane
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('id', $values['id'])
            ->update($values);
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
            ->table(ProjectModel::TABLE)
            ->eq('id', $values['id'])
            ->update(array(
                'default_swimlane' => $values['default_swimlane'],
                'show_default_swimlane' => $values['show_default_swimlane'],
            ));
    }

    /**
     * Enable the default swimlane
     *
     * @access public
     * @param  integer  $project_id
     * @return bool
     */
    public function enableDefault($project_id)
    {
        return $this->db
            ->table(ProjectModel::TABLE)
            ->eq('id', $project_id)
            ->update(array(
                'show_default_swimlane' => 1,
            ));
    }

    /**
     * Disable the default swimlane
     *
     * @access public
     * @param  integer  $project_id
     * @return bool
     */
    public function disableDefault($project_id)
    {
        return $this->db
            ->table(ProjectModel::TABLE)
            ->eq('id', $project_id)
            ->update(array(
                'show_default_swimlane' => 0,
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
        return $this->db
            ->table(self::TABLE)
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
        $this->db->table(TaskModel::TABLE)->eq('swimlane_id', $swimlane_id)->update(array('swimlane_id' => 0));

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
        $swimlanes = $this->db
            ->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->eq('is_active', 1)
            ->asc('position')
            ->asc('id')
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
     * Change swimlane position
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $swimlane_id
     * @param  integer  $position
     * @return boolean
     */
    public function changePosition($project_id, $swimlane_id, $position)
    {
        if ($position < 1 || $position > $this->db->table(self::TABLE)->eq('project_id', $project_id)->count()) {
            return false;
        }

        $swimlane_ids = $this->db->table(self::TABLE)
            ->eq('is_active', 1)
            ->eq('project_id', $project_id)
            ->neq('id', $swimlane_id)
            ->asc('position')
            ->findAllByColumn('id');

        $offset = 1;
        $results = array();

        foreach ($swimlane_ids as $current_swimlane_id) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->db->table(self::TABLE)->eq('id', $current_swimlane_id)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table(self::TABLE)->eq('id', $swimlane_id)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }

    /**
     * Duplicate Swimlane to project
     *
     * @access public
     * @param   integer    $project_from      Project Template
     * @param   integer    $project_to        Project that receives the copy
     * @return  integer|boolean
     */

    public function duplicate($project_from, $project_to)
    {
        $swimlanes = $this->getAll($project_from);

        foreach ($swimlanes as $swimlane) {
            unset($swimlane['id']);
            $swimlane['project_id'] = $project_to;

            if (! $this->db->table(self::TABLE)->save($swimlane)) {
                return false;
            }
        }

        $default_swimlane = $this->getDefault($project_from);
        $default_swimlane['id'] = $project_to;

        $this->updateDefault($default_swimlane);

        return true;
    }
}
