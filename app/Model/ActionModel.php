<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Action Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ActionModel extends Base
{
    /**
     * SQL table name for actions
     *
     * @var string
     */
    const TABLE = 'actions';

    /**
     * Return actions and parameters for a given user
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getAllByUser($user_id)
    {
        $project_ids = $this->projectPermissionModel->getActiveProjectIds($user_id);
        $actions = array();

        if (! empty($project_ids)) {
            $actions = $this->db->table(self::TABLE)->in('project_id', $project_ids)->findAll();
            $params = $this->actionParameterModel->getAllByActions(array_column($actions, 'id'));
            $this->attachParamsToActions($actions, $params);
        }

        return $actions;
    }

    /**
     * Return actions and parameters for a given project
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getAllByProject($project_id)
    {
        $actions = $this->db->table(self::TABLE)->eq('project_id', $project_id)->findAll();
        $params = $this->actionParameterModel->getAllByActions(array_column($actions, 'id'));
        return $this->attachParamsToActions($actions, $params);
    }

    /**
     * Return all actions and parameters
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        $actions = $this->db->table(self::TABLE)->findAll();
        $params = $this->actionParameterModel->getAll();
        return $this->attachParamsToActions($actions, $params);
    }

    /**
     * Fetch an action
     *
     * @access public
     * @param  integer $action_id
     * @return array
     */
    public function getById($action_id)
    {
        $action = $this->db->table(self::TABLE)->eq('id', $action_id)->findOne();

        if (! empty($action)) {
            $action['params'] = $this->actionParameterModel->getAllByAction($action_id);
        }

        return $action;
    }

    /**
     * Get the projectId by the actionId
     *
     * @access public
     * @param  integer $action_id
     * @return integer
     */
    public function getProjectId($action_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $action_id)->findOneColumn('project_id') ?: 0;
    }

    /**
     * Attach parameters to actions
     *
     * @access private
     * @param  array  &$actions
     * @param  array  &$params
     * @return array
     */
    private function attachParamsToActions(array &$actions, array &$params)
    {
        foreach ($actions as &$action) {
            $action['params'] = isset($params[$action['id']]) ? $params[$action['id']] : array();
        }

        return $actions;
    }

    /**
     * Remove an action
     *
     * @access public
     * @param  integer $action_id
     * @return bool
     */
    public function remove($action_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $action_id)->remove();
    }

    /**
     * Create an action
     *
     * @access public
     * @param  array   $values  Required parameters to save an action
     * @return boolean|integer
     */
    public function create(array $values)
    {
        $this->db->startTransaction();

        $action = array(
            'project_id' => $values['project_id'],
            'event_name' => $values['event_name'],
            'action_name' => $values['action_name'],
        );

        if (! $this->db->table(self::TABLE)->insert($action)) {
            $this->db->cancelTransaction();
            return false;
        }

        $action_id = $this->db->getLastId();

        if (! $this->actionParameterModel->create($action_id, $values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        return $action_id;
    }

    /**
     * Copy actions from a project to another one (skip actions that cannot resolve parameters)
     *
     * @author Antonio Rabelo
     * @param  integer    $src_project_id      Source project id
     * @param  integer    $dst_project_id      Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $actions = $this->actionModel->getAllByProject($src_project_id);

        foreach ($actions as $action) {
            $this->db->startTransaction();

            $values = array(
                'project_id' => $dst_project_id,
                'event_name' => $action['event_name'],
                'action_name' => $action['action_name'],
            );

            if (! $this->db->table(self::TABLE)->insert($values)) {
                $this->db->cancelTransaction();
                continue;
            }

            $action_id = $this->db->getLastId();

            if (! $this->actionParameterModel->duplicateParameters($dst_project_id, $action_id, $action['params'])) {
                $this->logger->error('Action::duplicate => skip action '.$action['action_name'].' '.$action['id']);
                $this->db->cancelTransaction();
                continue;
            }

            $this->db->closeTransaction();
        }

        return true;
    }
}
