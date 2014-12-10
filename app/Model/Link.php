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
    const TABLE_TASKS_LINKS = 'tasks_links';

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
        return $this->db->table(self::TABLE_TASKS_LINKS)->eq('id', $link_id)->findOne();
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
     * Get the link name by the id
     *
     * @access public
     * @param  integer   $link_id    Category id
     * @return string
     */
    public function getInversedNameById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOneColumn('name_inverse') ?: '';
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
                        ->beginOr()
                        ->eq('name', $link_name)
                        ->eq('name_inverse', $link_name)
                        ->closeOr()
                        ->findOneColumn('id');
    }

    /**
     * Return the list of all links
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
            $prepend[-1] = t('All links');
        }

        if ($prepend_none) {
            $prepend[0] = t('No link');
        }

        return $prepend + $listing;
    }

    /**
     * Return all links for a given project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getAll($task_id)
    {
//         $sql = '
//             SELECT
//             '.self::TABLE_TASKS_LINKS.'.*,
//             '.self::TABLE.'.*
//             FROM '.self::TABLE_TASKS_LINKS.'
//             JOIN '.self::TABLE.' ON '.self::TABLE.'.id = link_id
//             WHERE task_id = ? OR task_inverse_id = ?
//         ';
// //             '.Task::TABLE.'.title AS task_name
// //             LEFT JOIN '.Task::TABLE.' ON ('.Task::TABLE.'.id != ? AND ('.Task::TABLE.'.id == '.self::TABLE_TASKS_LINKS.'.task_id OR '.Task::TABLE.'.id == '.self::TABLE_TASKS_LINKS.'.task__inverse_id))
//         $rq = $this->db->execute($sql, array($task_id, $task_id));
//         var_dump($rq);
//         var_dump($sql);
//         $res = $rq->fetch(PDO::FETCH_ASSOC);
//         var_dump($res);
//         return $res;
        
        return $this->db->table(self::TABLE_TASKS_LINKS)
            ->beginOr()
            ->eq('task_id', $task_id)
            ->eq('task_inverse_id', $task_id)
            ->closeOr()
            ->columns(self::TABLE_TASKS_LINKS.'.*', self::TABLE.".*", Task::TABLE.".title AS task_name")
            ->join(self::TABLE, 'id', 'link_id')
            ->join(Task::TABLE, 'id', 'task_id')
            ->asc(self::TABLE.'.name')
            ->findAll();
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
        return $this->persist(self::TABLE_TASKS_LINKS, $values);
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
        return $this->db->table(self::TABLE_TASKS_LINKS)->eq('id', $values['id'])->save($values);
    }

    /**
     * Remove a link
     *
     * @access public
     * @param  integer   $link_id    Category id
     * @return bool
     */
    public function remove($link_id)
    {
        $this->db->startTransaction();

        $this->db->table(Task::TABLE)->eq('link_id', $link_id)->update(array('link_id' => 0));

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
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('task_inverse_id', t('The linked task id is required')),
            new Validators\Required('link_id', t('The link type is required')),
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
            new Validators\Required('name', t('The name is required')),
            new Validators\Required('name_inverse', t('The inversed name is required')),
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
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50),
            new Validators\MaxLength('name_inverse', t('The maximum length is %d characters', 50), 50),
        );
    }
}
