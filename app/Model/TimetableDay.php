<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Timetable Workweek
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TimetableDay extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'timetable_day';

    /**
     * Get the timetable for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return array
     */
    public function getByUser($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->asc('start')->findAll();
    }

    /**
     * Add a new time slot in the database
     *
     * @access public
     * @param  integer   $user_id   User id
     * @param  string    $start     Start hour (24h format)
     * @param  string    $end       End hour (24h format)
     * @return boolean|integer
     */
    public function create($user_id, $start, $end)
    {
        $values = array(
            'user_id' => $user_id,
            'start' => $start,
            'end' => $end,
        );

        return $this->persist(self::TABLE, $values);
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
        return $this->db->table(self::TABLE)->eq('id', $slot_id)->remove();
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
            new Validators\Required('start', t('Field required')),
            new Validators\Required('end', t('Field required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
