<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

/**
 * Project Duplication
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 * @author   Antonio Rabelo
 */
class ProjectDuplicationModel extends Base
{
    /**
     * Get list of optional models to duplicate
     *
     * @access public
     * @return string[]
     */
    public function getOptionalSelection()
    {
        return array(
            'categoryModel',
            'projectPermissionModel',
            'actionModel',
            'tagDuplicationModel',
            'projectMetadataModel',
            'projectTaskDuplicationModel',
        );
    }

    /**
     * Get list of all possible models to duplicate
     *
     * @access public
     * @return string[]
     */
    public function getPossibleSelection()
    {
        return array(
            'swimlaneModel',
            'boardModel',
            'categoryModel',
            'projectPermissionModel',
            'actionModel',
            'swimlaneModel',
            'tagDuplicationModel',
            'projectMetadataModel',
            'projectTaskDuplicationModel',
        );
    }

    /**
     * Get a valid project name for the duplication
     *
     * @access public
     * @param  string   $name         Project name
     * @param  integer  $max_length   Max length allowed
     * @return string
     */
    public function getClonedProjectName($name, $max_length = 50)
    {
        $suffix = ' ('.t('Clone').')';

        if (strlen($name.$suffix) > $max_length) {
            $name = substr($name, 0, $max_length - strlen($suffix));
        }

        return $name.$suffix;
    }

    /**
     * Clone a project with all settings
     *
     * @param  integer    $src_project_id       Project Id
     * @param  array      $selection            Selection of optional project parts to duplicate
     * @param  integer    $owner_id             Owner of the project
     * @param  string     $name                 Name of the project
     * @param  boolean    $private              Force the project to be private
     * @return integer                          Cloned Project Id
     */
    public function duplicate($src_project_id, $selection = array('projectPermissionModel', 'categoryModel', 'actionModel'), $owner_id = 0, $name = null, $private = null)
    {
        $this->db->startTransaction();

        // Get the cloned project Id
        $dst_project_id = $this->copy($src_project_id, $owner_id, $name, $private);

        if ($dst_project_id === false) {
            $this->db->cancelTransaction();
            return false;
        }

        // Clone Swimlanes, Columns, Categories, Permissions and Actions
        foreach ($this->getPossibleSelection() as $model) {

            // Skip if optional part has not been selected
            if (in_array($model, $this->getOptionalSelection()) && ! in_array($model, $selection)) {
                continue;
            }

            // Skip permissions for private projects
            if ($private && $model === 'projectPermissionModel') {
                continue;
            }

            if (! $this->$model->duplicate($src_project_id, $dst_project_id)) {
                $this->db->cancelTransaction();
                return false;
            }
        }

        if (! $this->makeOwnerManager($dst_project_id, $owner_id)) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        return (int) $dst_project_id;
    }

    /**
     * Create a project from another one
     *
     * @access private
     * @param  integer    $src_project_id
     * @param  integer    $owner_id
     * @param  string     $name
     * @param  boolean    $private
     * @return integer
     */
    private function copy($src_project_id, $owner_id = 0, $name = null, $private = null)
    {
        $project = $this->projectModel->getById($src_project_id);
        $is_private = empty($project['is_private']) ? 0 : 1;

        $values = array(
            'name' => $name ?: $this->getClonedProjectName($project['name']),
            'is_active' => 1,
            'last_modified' => time(),
            'token' => '',
            'is_public' => 0,
            'is_private' => $private ? 1 : $is_private,
            'owner_id' => $owner_id,
            'priority_default' => $project['priority_default'],
            'priority_start' => $project['priority_start'],
            'priority_end' => $project['priority_end'],
        );

        return $this->db->table(ProjectModel::TABLE)->persist($values);
    }

    /**
     * Make sure that the creator of the duplicated project is also owner
     *
     * @access private
     * @param  integer $dst_project_id
     * @param  integer $owner_id
     * @return boolean
     */
    private function makeOwnerManager($dst_project_id, $owner_id)
    {
        if ($owner_id > 0) {
            $this->projectUserRoleModel->removeUser($dst_project_id, $owner_id);

            if (! $this->projectUserRoleModel->addUser($dst_project_id, $owner_id, Role::PROJECT_MANAGER)) {
                return false;
            }
        }

        return true;
    }
}
