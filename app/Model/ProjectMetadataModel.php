<?php

namespace Kanboard\Model;

/**
 * Project Metadata
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectMetadataModel extends MetadataModel
{
    /**
     * Get the table
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getTable()
    {
        return 'project_has_metadata';
    }

    /**
     * Define the entity key
     *
     * @access protected
     * @return string
     */
    protected function getEntityKey()
    {
        return 'project_id';
    }

    /**
     * Helper method to duplicate all metadata to another project
     *
     * @access public
     * @param  integer $src_project_id
     * @param  integer $dst_project_id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $metadata = $this->getAll($src_project_id);

        if (! $this->save($dst_project_id, $metadata)) {
            return false;
        }

        return true;
    }
}
