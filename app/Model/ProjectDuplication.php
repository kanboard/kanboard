<?php

namespace Model;

/**
 * Project Duplication
 *
 * @package  model
 * @author   Frederic Guillot
 * @author   Antonio Rabelo
 */
class ProjectDuplication extends Base
{
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
     * Create a project from another one
     *
     * @param  integer    $project_id      Project Id
     * @return integer                     Cloned Project Id
     */
    public function copy($project_id)
    {
        $project = $this->project->getById($project_id);

        $values = array(
            'name' => $this->getClonedProjectName($project['name']),
            'is_active' => true,
            'last_modified' => 0,
            'token' => '',
            'is_public' => 0,
            'is_private' => empty($project['is_private']) ? 0 : 1,
        );

        if (! $this->db->table(Project::TABLE)->save($values)) {
            return 0;
        }

        return $this->db->getConnection()->getLastId();
    }

    /**
     * Clone a project with all settings
     *
     * @param  integer    $project_id  Project Id
     * @return integer                 Cloned Project Id
     */
    public function duplicate($project_id)
    {
        $this->db->startTransaction();

        // Get the cloned project Id
        $clone_project_id = $this->copy($project_id);

        if (! $clone_project_id) {
            $this->db->cancelTransaction();
            return false;
        }

        foreach (array('board', 'category', 'projectPermission', 'action') as $model) {

            if (! $this->$model->duplicate($project_id, $clone_project_id)) {
                $this->db->cancelTransaction();
                return false;
            }
        }

        $this->db->closeTransaction();

        return (int) $clone_project_id;
    }
}
