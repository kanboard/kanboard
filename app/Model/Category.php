<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Category model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Category extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_has_categories';

    /**
     * Return true if a category exists for a given project
     *
     * @access public
     * @param  integer   $category_id    Category id
     * @param  integer   $project_id     Project id
     * @return boolean
     */
    public function exists($category_id, $project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $category_id)->eq('project_id', $project_id)->count() > 0;
    }

    /**
     * Get a category by the id
     *
     * @access public
     * @param  integer   $category_id    Category id
     * @return array
     */
    public function getById($category_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $category_id)->findOne();
    }

    /**
     * Get the category name by the id
     *
     * @access public
     * @param  integer   $category_id    Category id
     * @return string
     */
    public function getNameById($category_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $category_id)->findOneColumn('name') ?: '';
    }

    /**
     * Get a category id by the project and the name
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @param  string    $category_name   Category name
     * @return integer
     */
    public function getIdByName($project_id, $category_name)
    {
        return (int) $this->db->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('name', $category_name)
                        ->findOneColumn('id');
    }

    /**
     * Return the list of all categories
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
            ->asc('name')
            ->listing('id', 'name');

        $prepend = array();

        if ($prepend_all) {
            $prepend[-1] = t('All categories');
        }

        if ($prepend_none) {
            $prepend[0] = t('No category');
        }

        return $prepend + $listing;
    }

    /**
     * Return all categories for a given project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    /**
     * Create a category
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool|integer
     */
    public function create(array $values)
    {
        return $this->persist(self::TABLE, $values);
    }

    /**
     * Update a category
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);
    }

    /**
     * Remove a category
     *
     * @access public
     * @param  integer   $category_id    Category id
     * @return bool
     */
    public function remove($category_id)
    {
        $this->db->startTransaction();

        $this->db->table(Task::TABLE)->eq('category_id', $category_id)->update(array('category_id' => 0));

        if (! $this->db->table(self::TABLE)->eq('id', $category_id)->remove()) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Duplicate categories from a project to another one, must be executed inside a transaction
     *
     * @author Antonio Rabelo
     * @param  integer    $src_project_id        Source project id
     * @return integer    $dst_project_id        Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $categories = $this->db->table(self::TABLE)
                               ->columns('name')
                               ->eq('project_id', $src_project_id)
                               ->asc('name')
                               ->findAll();

        foreach ($categories as $category) {

            $category['project_id'] = $dst_project_id;

            if (! $this->db->table(self::TABLE)->save($category)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate category creation
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
     * Validate category modification
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
