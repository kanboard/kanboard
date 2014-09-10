<?php

namespace Model;

use PDO;
use Core\Template;

/**
 * Task history model
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class BaseHistory extends Base
{
    /**
     * SQL table name
     *
     * @access protected
     * @var    string
     */
    protected $table = '';

    /**
     * Remove old event entries to avoid a large table
     *
     * @access public
     * @param  integer    $max    Maximum number of items to keep in the table
     */
    public function cleanup($max)
    {
        if ($this->db->table($this->table)->count() > $max) {

            $this->db->execute('
                DELETE FROM '.$this->table.'
                WHERE id <= (
                    SELECT id FROM (
                        SELECT id FROM '.$this->table.' ORDER BY id DESC LIMIT 1 OFFSET '.$max.'
                    ) foo
                )');
        }
    }

    /**
     * Get all events for a given project
     *
     * @access public
     * @return array
     */
    public function getAllByProjectId($project_id)
    {
        return $this->db->table($this->table)
                        ->eq('project_id', $project_id)
                        ->desc('id')
                        ->findAll();
    }

    /**
     * Get the event html content
     *
     * @access public
     * @param  array     $params    Event properties
     * @return string
     */
    public function getContent(array $params)
    {
        $tpl = new Template;
        return $tpl->load('event_'.str_replace('.', '_', $params['event_name']), $params);
    }
}
