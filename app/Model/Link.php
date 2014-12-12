<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PDO;
/**
 * Link model
 *
 * @package  model
 * @author   Olivier Maridat
 */
class Link extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'links';

    /**
     * Return true if a link exists for a given project
     *
     * @access public
     * @param  integer   $link_id    Category id
     * @param  integer   $project_id     Project id
     * @return boolean
     */
    public function exists($link_id, $project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->eq('project_id', $project_id)->count() > 0;
    }

    /**
     * Get a link by the id
     *
     * @access public
     * @param  integer   $link_id    Category id
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOne();
    }

    /**
     * Get the link name by the id
     *
     * @access public
     * @param  integer   $link_id    Category id
     * @return string
     */
    public function getNameById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOneColumn('name') ?: '';
    }
    
    /**
     * Get a link id by the project and the name
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @param  string    $link_name   Category name
     * @return integer
     */
    public function getIdByName($project_id, $link_name)
    {
        return (int) $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('name', $link_name)
                        ->findOneColumn('id');
    }

    /**
     * Return the list of all links for a given project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  bool      $prepend_none  If true, prepend to the list the value 'None'
     * @param  bool      $prepend_all   If true, prepend to the list the value 'All'
     * @return array
     */
    public function getList($project_id, $prepend_none = true, $prepend_all = false)
    {
        $listing = $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('id')
            ->listing('id', 'name');

        $prepend = array();

        if ($prepend_all) {
            $prepend[-1] = t('All links');
        }

        if ($prepend_none) {
            $prepend[0] = t('No link');
        }

        return $prepend + $listing;
    }

    /**
     * Return all links
     * @access public
     * @param  bool      $prepend_none  If true, prepend to the list the value 'None'
     * @param  bool      $prepend_all   If true, prepend to the list the value 'All'
     * @return array
     */
    public function getAll($prepend_none = false, $prepend_all = false)
    {
        $listing = $this->db->table(self::TABLE)
            ->asc('name')
            ->listing('id', 'name');

        $prepend = array();

        if ($prepend_all) {
            $prepend[-1] = t('All links');
        }

        if ($prepend_none) {
            $prepend[0] = t('No link');
        }

        return $prepend + $listing;
    }
    
    /**
     * Prepare data before insert/update
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $this->removeFields($values, array('another_link'));
    }
    

    /**
     * Create a link
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool|integer
     */
    public function create(array $values)
    {
        $this->prepare($values);
        return $this->persist(self::TABLE, $values);
    }

    /**
     * Update a link
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        $this->prepare($values);
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);
    }

    /**
     * Remove a link
     *
     * @access public
     * @param  integer   $link_id    Link id
     * @return bool
     */
    public function remove($link_id)
    {
        $this->db->startTransaction();

        $this->db->table(Task::TABLE)->eq('id', $link_id)->update(array('id' => 0));

        if (! $this->db->table(self::TABLE)->eq('id', $link_id)->remove()) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Duplicate links from a project to another one, must be executed inside a transaction
     *
     * @author Antonio Rabelo
     * @param  integer    $src_project_id        Source project id
     * @return integer    $dst_project_id        Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $links = $this->db->table(self::TABLE)
                               ->columns('name')
                               ->eq('project_id', $src_project_id)
                               ->asc('name')
                               ->findAll();

        foreach ($links as $link) {

            $link['project_id'] = $dst_project_id;

            if (! $this->db->table(self::TABLE)->save($link)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate link creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('name', t('The link name is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate link modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Required('name', t('The link name is required')),
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
            new Validators\MaxLength('name', t('The maximum length is %d characters', 150), 150),
        );
    }
}
