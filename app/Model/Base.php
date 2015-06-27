<?php

namespace Model;

use Pimple\Container;

/**
 * Base model class
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class Base extends \Core\Base
{
    /**
     * Database instance
     *
     * @access protected
     * @var \PicoDb\Database
     */
    protected $db;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->db = $this->container['db'];
    }

    /**
     * Save a record in the database
     *
     * @access public
     * @param  string            $table      Table name
     * @param  array             $values     Form values
     * @return boolean|integer
     */
    public function persist($table, array $values)
    {
        return $this->db->transaction(function($db) use ($table, $values) {

            if (! $db->table($table)->save($values)) {
                return false;
            }

            return (int) $db->getLastId();
        });
    }

    /**
     * Remove keys from an array
     *
     * @access public
     * @param  array     $values    Input array
     * @param  string[]  $keys      List of keys to remove
     */
    public function removeFields(array &$values, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                unset($values[$key]);
            }
        }
    }

    /**
     * Force some fields to be at 0 if empty
     *
     * @access public
     * @param  array        $values    Input array
     * @param  string[]     $keys      List of keys
     */
    public function resetFields(array &$values, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($values[$key]) && empty($values[$key])) {
                $values[$key] = 0;
            }
        }
    }

    /**
     * Force some fields to be integer
     *
     * @access public
     * @param  array        $values    Input array
     * @param  string[]     $keys      List of keys
     */
    public function convertIntegerFields(array &$values, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                $values[$key] = (int) $values[$key];
            }
        }
    }

    /**
     * Build SQL condition for a given time range
     *
     * @access protected
     * @param  string   $start_time     Start timestamp
     * @param  string   $end_time       End timestamp
     * @param  string   $start_column   Start column name
     * @param  string   $end_column     End column name
     * @return string
     */
    protected function getCalendarCondition($start_time, $end_time, $start_column, $end_column)
    {
        $start_column = $this->db->escapeIdentifier($start_column);
        $end_column = $this->db->escapeIdentifier($end_column);

        $conditions = array(
            "($start_column >= '$start_time' AND $start_column <= '$end_time')",
            "($start_column <= '$start_time' AND $end_column >= '$start_time')",
            "($start_column <= '$start_time' AND ($end_column = '0' OR $end_column IS NULL))",
        );

        return $start_column.' IS NOT NULL AND '.$start_column.' > 0 AND ('.implode(' OR ', $conditions).')';
    }

    /**
     * Get common properties for task calendar events
     *
     * @access protected
     * @param  array  $task
     * @return array
     */
    protected function getTaskCalendarProperties(array &$task)
    {
        return array(
            'timezoneParam' => $this->config->getCurrentTimezone(),
            'id' => $task['id'],
            'title' => t('#%d', $task['id']).' '.$task['title'],
            'backgroundColor' => $this->color->getBackgroundColor($task['color_id']),
            'borderColor' => $this->color->getBorderColor($task['color_id']),
            'textColor' => 'black',
            'url' => $this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
        );
    }

    /**
     * Group a collection of records by a column
     *
     * @access public
     * @param  array   $collection
     * @param  string  $column
     * @return array
     */
    public function groupByColumn(array $collection, $column)
    {
        $result = array();

        foreach ($collection as $item) {
            $result[$item[$column]][] = $item;
        }

        return $result;
    }
}
