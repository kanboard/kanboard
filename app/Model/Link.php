<?php
namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PDO;

/**
 * Link model
 *
 * @package model
 * @author Olivier Maridat
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
     * @param integer $link_id
     *            Category id
     * @param integer $project_id
     *            Project id
     * @return boolean
     */
    public function exists($link_id, $project_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $link_id)
            ->eq('project_id', $project_id)
            ->count() > 0;
    }

    /**
     * Get a link by the id
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $link_id)
            ->findOne();
    }

    /**
     * Get a link (name + name inverse) by the id
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @return array
     */
    public function getMergedById($link_id)
    {
        $link_id2 = (0 == ($link_id % 2)) ? $link_id - 1 : $link_id + 1;
        $link = $this->db->table(self::TABLE)
            ->eq('id', $link_id)
            ->findOne();
        $link2 = $this->db->table(self::TABLE)
            ->eq('id', $link_id2)
            ->findOne();
        if (! $link || ! $link2) {
            return false;
        }
        // Link is inverse
        if (0 == ($link_id % 2)) {
            $link['name_inverse'] = $link['name'];
            $link['name'] = $link2['name'];
        }
        else {
            $link['name_inverse'] = $link2['name'];
        }
        return $link;
    }

    /**
     * Get the link name by the id
     *
     * @access public
     * @param integer $link_id
     *            Category id
     * @return string
     */
    public function getNameById($link_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $link_id)
            ->findOneColumn('name') ?  : '';
    }

    /**
     * Get a link id by the project and the name
     *
     * @access public
     * @param integer $project_id
     *            Project id
     * @param string $link_name
     *            Category name
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
     * @param integer $project_id
     *            Project id
     * @param bool $prepend_none
     *            If true, prepend to the list the value 'None'
     * @param bool $prepend_all
     *            If true, prepend to the list the value 'All'
     * @return array
     */
    public function getList($project_id, $prepend_none = true, $prepend_all = false)
    {
        $listing = $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('id')
            ->listing('id', 'name');
        foreach($listing AS $id => $name) {
            if (0 != ($id%2)) {
                $listing[$id] .= ' &raquo;';
            }
            else {
                $listing[$id] = '&laquo; '.$name;
            }
        }
        $prepend = array();
        
        if ($prepend_all) {
            $prepend[- 1] = t('All links');
        }
        
        if ($prepend_none) {
            $prepend[0] = t('No link');
        }
        
        return $prepend + $listing;
    }

    /**
     * Return the list of all links (name + inverse name) for a given project
     *
     * @access public
     * @param integer $project_id
     *            Project id
     * @return array
     */
    public function getMergedList($project_id)
    {
        $listing = $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('id')
            ->column('id', 'name')
            ->findAll();
        
        $mergedListing = array();
        $current = null;
        foreach ($listing as $link) {
            if (0 != ($link['id'] % 2)) {
                $current = $link;
            }
            else {
                $current['name_inverse'] = $link['name'];
                $mergedListing[] = $current;
                $current = null;
            }
        }
        $listing = $mergedListing;
        return $listing;
    }

    /**
     * Return all links
     * 
     * @access public
     * @param bool $prepend_none
     *            If true, prepend to the list the value 'None'
     * @param bool $prepend_all
     *            If true, prepend to the list the value 'All'
     * @return array
     */
    public function getAll($prepend_none = false, $prepend_all = false)
    {
        $listing = $this->db->table(self::TABLE)
            ->asc('name')
            ->listing('id', 'name');
        
        $prepend = array();
        
        if ($prepend_all) {
            $prepend[- 1] = t('All links');
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
     * @param array $values
     *            Form values
     */
    public function prepare(array &$values)
    {
        $link1 = array(
            'project_id' => $values['project_id'],
            'name' => $values['name'],
            'is_inverse' => 0
        );
        $link2 = array(
            'project_id' => $values['project_id'],
            'name' => $values['name_inverse'],
            'is_inverse' => 1
        );
        $link_id1 = 0;
        $link_id2 = 0;
        if (isset($values['id'])) {
            $link_id1 = (0 == ($values['id'] % 2) ? $values['id'] - 1 : $values['id']);
            $link_id2 = (0 == ($values['id'] % 2) ? $values['id'] : $values['id'] + 1);
        }
        return array(
            $link1,
            $link2,
            $link_id1,
            $link_id2
        );
    }

    /**
     * Create a link
     *
     * @access public
     * @param array $values
     *            Form values
     * @return bool integer
     */
    public function create(array $values)
    {
        list ($link1, $link2) = $this->prepare($values);
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)->save($link1);
        $res *= $this->db->table(self::TABLE)->save($link2);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return $res;
    }

    /**
     * Update a link
     *
     * @access public
     * @param array $values
     *            Form values
     * @return bool
     */
    public function update(array $values)
    {
        list ($link1, $link2, $link_id1, $link_id2) = $this->prepare($values);
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)
            ->eq('id', $link_id1)
            ->save($link1);
        $res *= $this->db->table(self::TABLE)
            ->eq('id', $link_id2)
            ->save($link2);
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return $res;
    }

    /**
     * Remove a link
     *
     * @access public
     * @param integer $link_id
     *            Link id
     * @return bool
     */
    public function remove($link_id)
    {
        $this->db->startTransaction();
        $res = $this->db->table(self::TABLE)
            ->eq('id', $link_id)
            ->remove();
        $res *= $this->db->table(self::TABLE)
            ->eq('id', (0 == ($link_id % 2)) ? $link_id - 1 : $link_id + 1)
            ->remove();
        if (! $res) {
            $this->db->cancelTransaction();
            return false;
        }
        $this->db->closeTransaction();
        return $res;
    }

    /**
     * Duplicate links from a project to another one, must be executed inside a transaction
     *
     * @param integer $src_project_id
     *            Source project id
     * @return integer $dst_project_id Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $links = $this->db->table(self::TABLE)
            ->columns('project_id', 'name', 'is_inverse')
            ->eq('project_id', $src_project_id)
            ->asc('id')
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
     * @param array $values
     *            Form values
     * @return array $valid, $errors [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, $this->commonValidationRules());
        
        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate link modification
     *
     * @access public
     * @param array $values
     *            Form values
     * @return array $valid, $errors [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required'))
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
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Required('name', t('The link label is required')),
            new Validators\Required('name_inverse', t('The link inverse label is required')),
            new Validators\Integer('id', t('The link id must be an integer')),
            new Validators\Integer('project_id', t('The project id must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 150), 150),
            new Validators\MaxLength('name_inverse', t('The maximum length is %d characters', 150), 150)
        );
    }
}
