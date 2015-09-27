<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Custom Filter model
 *
 * @package  model
 * @author   Timo Litzbarski
 */
class CustomFilter extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'custom_filters';

    /**
     * Get query to fetch custom filters
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->db->table(self::TABLE)
                        ->columns(  User::TABLE.'.name as owner_name',
                                    User::TABLE.'.username as owner_username',
                                    self::TABLE.'.user_id',
                                    self::TABLE.'.project_id',
                                    self::TABLE.'.filter',
                                    self::TABLE.'.name',
                                    self::TABLE.'.is_shared'
                                )                                                
                        ->asc(self::TABLE.'name')
                        ->join(User::TABLE, 'id', 'user_id');                        
    }
    
    /**
     * Return the list of all shared custom filters for a specific project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getAllShared($project_id)
    {   
        return $this->getQuery()
                        ->eq('project_id', $project_id)
                        ->eq('is_shared', 1)
                        ->findAll();
    }
    
    /**
     * Return the list of all private custom filters for a user and project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $user_id       User id
     * @return array
     */
    public function getAllPrivate($project_id, $user_id)
    {
        return $this->getQuery()
                ->eq('project_id', $project_id)
                ->eq('user_id', $user_id)
                ->eq('is_shared', 0)
                ->findAll();
    }
    
    /**
     * Return the list of all allowed custom filters for a user and project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $user_id       User id
     * @return array
     */
    public function getAll($project_id, $user_id)
    {   
        $private = $this->getAllPrivate($project_id, $user_id);
        $shared = $this->getAllShared($project_id);
        return array_merge($private, $shared);
    }
    
    /**
     * Validate custom filter creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Required('user_id', t('The user id is required')),
            new Validators\Required('name', t('The name is required')),
            new Validators\Required('filter', t('The filter is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
    
    /**
     * Create a custom filter
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
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Integer('user_id', t('The user id must be an integer')),
            new Validators\Integer('project_id', t('The project id must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 80), 80),
            new Validators\MaxLength('filter', t('The maximum length is %d characters', 80), 80)
        );
    }
    
        
    /**
     * Get custom filter
     *
     * @access private
     * @param  integer   $project_id
     * @return array
     */
    public function getCustomFilter($filter, $project_id, $user_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('filter', $filter)
            ->findOne();
    }
    
    /**
     * Remove a custom filter
     *
     * @access public
     * @return bool
     */
    public function remove($filter, $project_id, $user_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('filter', $filter)
            ->remove();
    }
    
    /**
     * Validate custom filter modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Required('user_id', t('The user id is required')),
            new Validators\Required('name', t('The name is required')),
            new Validators\Required('filter', t('The filter is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
    
    /**
     * Update a custom filter
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values, $filter_original)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $values['project_id'])
            ->eq('user_id', $values['user_id'])
            ->eq('filter', $filter_original)
            ->save($values);
    }
}
