<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Hourly Rate
 *
 * @package  model
 * @author   Frederic Guillot
 */
class HourlyRate extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'hourly_rates';

    /**
     * Get all user rates for a given project
     *
     * @access public
     * @param  integer  $project_id
     * @return array
     */
    public function getAllByProject($project_id)
    {
        $members = $this->projectPermission->getMembers($project_id);

        if (empty($members)) {
            return array();
        }

        return $this->db->table(self::TABLE)->in('user_id', array_keys($members))->desc('date_effective')->findAll();
    }

    /**
     * Get all rates for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return array
     */
    public function getAllByUser($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->desc('date_effective')->findAll();
    }

    /**
     * Get current rate for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return float
     */
    public function getCurrentRate($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->desc('date_effective')->findOneColumn('rate') ?: 0;
    }

    /**
     * Add a new rate in the database
     *
     * @access public
     * @param integer   $user_id   User id
     * @param float     $rate      Hourly rate
     * @param string    $currency  Currency code
     * @param string    $date      ISO8601 date format
     * @return boolean|integer
     */
    public function create($user_id, $rate, $currency, $date)
    {
        $values = array(
            'user_id' => $user_id,
            'rate' => $rate,
            'currency' => $currency,
            'date_effective' => $this->dateParser->removeTimeFromTimestamp($this->dateParser->getTimestamp($date)),
        );

        return $this->persist(self::TABLE, $values);
    }

    /**
     * Remove a specific rate
     *
     * @access public
     * @param  integer    $rate_id
     * @return boolean
     */
    public function remove($rate_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $rate_id)->remove();
    }

    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('user_id', t('Field required')),
            new Validators\Required('rate', t('Field required')),
            new Validators\Numeric('rate', t('This value must be numeric')),
            new Validators\Required('date_effective', t('Field required')),
            new Validators\Required('currency', t('Field required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
