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
     * @param  integer   $swimlaneId
     * @return array
     */
    public function getById($swimlaneId)
    {
        return $this->db->table(self::TABLE)->eq('id', $swimlaneId)->findOne();
    }

    /**
     * Get the swimlane name by the id
     *
     * @access public
     * @param  integer   $swimlaneId
     * @return string
     */
    public function getNameById($swimlaneId)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $swimlaneId)
            ->findOneColumn('name');
    }

    /**
     * Get a swimlane id by the project and the name
     *
     * @access public
     * @param  integer   $projectId      Project id
     * @param  string    $name            Name
     * @return integer
     */
    public function getIdByName($projectId, $name)
    {
        return (int) $this->db->table(self::TABLE)
                        ->eq('project_id', $projectId)
                        ->eq('name', $name)
                        ->findOneColumn('id');
    }

    /**
     * Get a swimlane by the project and the name
     *
     * @access public
     * @param  integer   $projectId      Project id
     * @param  string    $name            Swimlane name
     * @return array
     */
    public function getByName($projectId, $name)
    {
        return $this->db->table(self::TABLE)
                        ->eq('project_id', $projectId)
                        ->eq('name', $name)
                        ->findOne();
    }

    /**
     * Get first active swimlane for a project
     *
     * @access public
     * @param  integer $projectId
     * @return array|null
     */
    public function getFirstActiveSwimlane($projectId)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $projectId)
            ->eq('is_active', 1)
            ->asc('position')
            ->findOne();
    }

    /**
     * Get first active swimlaneId
     *
     * @access public
     * @param  int $projectId
     * @return int
     */
    public function getFirstActiveSwimlaneId($projectId)
    {
        return (int) $this->db->table(self::TABLE)
            ->eq('project_id', $projectId)
            ->eq('is_active', 1)
            ->asc('position')
            ->findOneColumn('id');
    }

    /**
     * Get all swimlanes for a given project
     *
     * @access public
     * @param  integer   $projectId
     * @return array
     */
    public function getAll($projectId)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('project_id', $projectId)
            ->orderBy('position', 'asc')
            ->findAll();
    }

    /**
     * Get the list of swimlanes by status
     *
     * @access public
     * @param  integer   $projectId
     * @param  integer   $status
     * @return array
     */
    public function getAllByStatus($projectId, $status = self::ACTIVE)
    {
        $query = $this->db
            ->table(self::TABLE)
            ->eq('project_id', $projectId)
            ->eq('is_active', $status);

        if ($status == self::ACTIVE) {
            $query->asc('position');
        } else {
            $query->asc('name');
        }

        return $query->findAll();
    }

    /**
     * Get all swimlanes with task count
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return array
     */
    public function getAllWithTaskCount($project_id)
    {
        $result = array(
            'active' => array(),
            'inactive' => array(),
        );

        $swimlanes = $this->db->table(self::TABLE)
            ->columns('id', 'name', 'description', 'project_id', 'position', 'is_active')
            ->subquery("SELECT COUNT(*) FROM ".TaskModel::TABLE." WHERE swimlane_id=".self::TABLE.".id AND is_active='1'", 'nb_open_tasks')
            ->subquery("SELECT COUNT(*) FROM ".TaskModel::TABLE." WHERE swimlane_id=".self::TABLE.".id AND is_active='0'", 'nb_closed_tasks')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->asc('name')
            ->findAll();

        foreach ($swimlanes as $swimlane) {
            if ($swimlane['is_active']) {
                $result['active'][] = $swimlane;
            } else {
                $result['inactive'][] = $swimlane;
            }
        }

        return $result;
    }

    /**
     * Get list of all swimlanes
     *
     * @access public
     * @param  integer   $projectId    Project id
     * @param  boolean   $prepend      Prepend default value
     * @param  boolean   $onlyActive   Return only active swimlanes
     * @return array
     */
    public function getList($projectId, $prepend = false, $onlyActive = false)
    {
        $swimlanes = array();

        if ($prepend) {
            $swimlanes[-1] = t('All swimlanes');
        }

        return $swimlanes + $this->db
            ->hashtable(self::TABLE)
            ->eq('project_id', $projectId)
            ->in('is_active', $onlyActive ? array(self::ACTIVE) : array(self::ACTIVE, self::INACTIVE))
            ->orderBy('position', 'asc')
            ->getAll('id', 'name');
    }

    /**
     * Add a new swimlane
     *
     * @access public
     * @param  int     $projectId
     * @param  string  $name
     * @param  string  $description
     * @return bool|int
     */
    public function create($projectId, $name, $description = '')
    {
        if (! $this->projectModel->exists($projectId)) {
            return 0;
        }

        return $this->db->table(self::TABLE)->persist(array(
            'project_id'  => $projectId,
            'name'        => $name,
            'description' => $description,
            'position'    => $this->getLastPosition($projectId),
            'is_active'   => 1,
        ));
    }

    /**
     * Update a swimlane
     *
     * @access public
     * @param  integer $swimlaneId
     * @param  array   $values
     * @return bool
     */
    public function update($swimlaneId, array $values)
    {
        unset($values['id']);
        unset($values['project_id']);

        return $this->db
            ->table(self::TABLE)
            ->eq('id', $swimlaneId)
            ->update($values);
    }

    /**
     * Get the last position of a swimlane
     *
     * @access public
     * @param  integer   $projectId
     * @return integer
     */
    public function getLastPosition($projectId)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('project_id', $projectId)
            ->eq('is_active', 1)
            ->count() + 1;
    }

    /**
     * Disable a swimlane
     *
     * @access public
     * @param  integer   $projectId
     * @param  integer   $swimlaneId
     * @return bool
     */
    public function disable($projectId, $swimlaneId)
    {
        $result = $this->db
            ->table(self::TABLE)
            ->eq('id', $swimlaneId)
            ->eq('project_id', $projectId)
            ->update(array(
                'is_active' => self::INACTIVE,
                'position' => 0,
            ));

        if ($result) {
            $this->updatePositions($projectId);
        }

        return $result;
    }

    /**
     * Enable a swimlane
     *
     * @access public
     * @param  integer   $projectId
     * @param  integer   $swimlaneId
     * @return bool
     */
    public function enable($projectId, $swimlaneId)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('id', $swimlaneId)
            ->eq('project_id', $projectId)
            ->update(array(
                'is_active' => self::ACTIVE,
                'position' => $this->getLastPosition($projectId),
            ));
    }

    /**
     * Remove a swimlane
     *
     * @access public
     * @param  integer   $projecId
     * @param  integer   $swimlaneId
     * @return bool
     */
    public function remove($projecId, $swimlaneId)
    {
        $this->db->startTransaction();

        if ($this->db->table(TaskModel::TABLE)->eq('swimlane_id', $swimlaneId)->exists()) {
            $this->db->cancelTransaction();
            return false;
        }

        if (! $this->db->table(self::TABLE)->eq('id', $swimlaneId)->remove()) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->updatePositions($projecId);
        $this->db->closeTransaction();

        return true;
    }

    /**
     * Update swimlane positions after disabling or removing a swimlane
     *
     * @access public
     * @param  integer  $projectId
     * @return boolean
     */
    public function updatePositions($projectId)
    {
        $position = 0;
        $swimlanes = $this->db
            ->table(self::TABLE)
            ->eq('project_id', $projectId)
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
     * @param  integer  $projectId
     * @param  integer  $swimlaneId
     * @param  integer  $position
     * @return boolean
     */
    public function changePosition($projectId, $swimlaneId, $position)
    {
        if ($position < 1 || $position > $this->db->table(self::TABLE)->eq('project_id', $projectId)->count()) {
            return false;
        }

        $swimlaneIds = $this->db->table(self::TABLE)
            ->eq('is_active', 1)
            ->eq('project_id', $projectId)
            ->neq('id', $swimlaneId)
            ->asc('position')
            ->findAllByColumn('id');

        $offset = 1;
        $results = array();

        foreach ($swimlaneIds as $currentSwimlaneId) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->db->table(self::TABLE)->eq('id', $currentSwimlaneId)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table(self::TABLE)->eq('id', $swimlaneId)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }

    /**
     * Duplicate Swimlane to project
     *
     * @access public
     * @param  integer    $projectSrcId
     * @param  integer    $projectDstId
     * @return boolean
     */
    public function duplicate($projectSrcId, $projectDstId)
    {
        $swimlanes = $this->getAll($projectSrcId);

        foreach ($swimlanes as $swimlane) {
            if (! $this->db->table(self::TABLE)->eq('project_id', $projectDstId)->eq('name', $swimlane['name'])->exists()) {
                $values = array(
                    'name'        => $swimlane['name'],
                    'description' => $swimlane['description'],
                    'position'    => $swimlane['position'],
                    'is_active'   => $swimlane['is_active'],
                    'project_id'  => $projectDstId,
                );

                if (! $this->db->table(self::TABLE)->persist($values)) {
                    return false;
                }
            }
        }

        return true;
    }
}
