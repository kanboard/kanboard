<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Category model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class CategoryModel extends Base
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
     * @return boolean
     */
    public function exists($category_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $category_id)->exists();
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
     * Get the projectId by the category id
     *
     * @access public
     * @param  integer   $category_id    Category id
     * @return integer
     */
    public function getProjectId($category_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $category_id)->findOneColumn('project_id') ?: 0;
    }

    /**
     * Get a category id by the category name and project id
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
        $listing = $this->db->hashtable(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('name')
            ->getAll('id', 'name');

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
     * Create default categories during project creation (transaction already started in Project::create())
     *
     * @access public
     * @param  integer  $project_id
     * @return boolean
     */
    public function createDefaultCategories($project_id)
    {
        $results = array();
        $categories = array_unique(explode_csv_field($this->configModel->get('project_categories')));

        foreach ($categories as $category) {
            $results[] = $this->db->table(self::TABLE)->insert(array(
                'project_id' => $project_id,
                'name' => $category,
            ));
        }

        return in_array(false, $results, true);
    }

    /**
     * Create a category (run inside a transaction)
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool|integer
     */
    public function create(array $values)
    {
        return $this->db->table(self::TABLE)->persist($values);
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

        $this->db->table(TaskModel::TABLE)->eq('category_id', $category_id)->update(array('category_id' => 0));

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
     * @param  integer    $dst_project_id        Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $categories = $this->db
            ->table(self::TABLE)
            ->columns('name', 'description', 'color_id')
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
}
