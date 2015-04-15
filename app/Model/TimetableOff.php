<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Timetable time off
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TimetableOff extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'timetable_off';

    /**
     * Get query to fetch everything (pagination)
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return \PicoDb\Table
     */
    public function getUserQuery($user_id)
    {
        return $this->db->table(static::TABLE)->eq('user_id', $user_id);
    }

    /**
     * Get the timetable for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return array
     */
    public function getByUser($user_id)
    {
        return $this->db->table(static::TABLE)->eq('user_id', $user_id)->desc('date')->asc('start')->findAll();
    }

    /**
     * Get the timetable for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @param  string   $start_date
     * @param  string   $end_date
     * @return array
     */
    public function getByUserAndDate($user_id, $start_date, $end_date)
    {
        return $this->db->table(static::TABLE)
                        ->eq('user_id', $user_id)
                        ->gte('date', $start_date)
                        ->lte('date', $end_date)
                        ->desc('date')
                        ->asc('start')
                        ->findAll();
    }

    /**
     * Add a new time slot in the database
     *
     * @access public
     * @param  integer   $user_id   User id
     * @param  string    $date      Day (ISO8601 format)
     * @param  boolean   $all_day   All day flag
     * @param  float     $start     Start hour (24h format)
     * @param  float     $end       End hour (24h format)
     * @param  string    $comment
     * @return boolean|integer
     */
    public function create($user_id, $date, $all_day, $start = '', $end = '', $comment = '')
    {
        $values = array(
            'user_id' => $user_id,
            'date' => $date,
            'all_day' => (int) $all_day, // Postgres fix
            'start' => $all_day ? '' : $start,
            'end' => $all_day ? '' : $end,
            'comment' => $comment,
        );

        return $this->persist(static::TABLE, $values);
    }

    /**
     * Remove a specific time slot
     *
     * @access public
     * @param  integer    $slot_id
     * @return boolean
     */
    public function remove($slot_id)
    {
        return $this->db->table(static::TABLE)->eq('id', $slot_id)->remove();
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
            new Validators\Required('date', t('Field required')),
            new Validators\Numeric('all_day', t('This value must be numeric')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
