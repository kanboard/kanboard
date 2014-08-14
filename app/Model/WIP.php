<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * WIP model
 *
 * @package  model
 * @author   Antonio Rabelo
 */
class WIP extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'columns_stats';
    
    /**
     * Save stats for a column and owner.
     * 
     * @author Antonio Rabelo
     * @param integer $project_id   Project Id
     * @param integer $column_id    Column Id
     * @param integer $owner_id     Owner Id
     * @return boolean
     */
    public function saveStatsByColumnAndTaskOwner($project_id, $column_id, $owner_id)
    {
    	$taskModel = new Task($this->db, $this->event);
    	$count_tasks = $taskModel->countByOwnerId($project_id, $column_id, $owner_id);
    	
    	// always store epoch from current date ($date) in UTC timezone
    	$actual_time_zone = date_default_timezone_get();
    	date_default_timezone_set('UTC');
		$date = strtotime(date('Y-m-d'));
		date_default_timezone_set($actual_time_zone);
				
		$column_stats = $this->getStats($date, $column_id, $owner_id);
    	
    	$values = array('quantity'  => $count_tasks,
		    			'date'      => $date,
		    			'column_id' => $column_id,
		    			'user_id'   => $owner_id
    	);

    	$table = $this->db->table(self::TABLE);
    	
    	if(!empty($column_stats)) {
    		$table->eq('id', $column_stats['id']);
    	}
    	
    	if (!$table->save($values)) {
    		return false;
    	}

    	return true;
    }
    
    /**
     * Get stats from column id and owner id.
     * 
     * @author Antonio Rabelo
     * @param date     $date
     * @param integer  $column_id
     * @param integer  $owner_id
     * @return array
     */
    public function getStats($date, $column_id, $owner_id)
    {
    	return $this->db
    		    	->table(self::TABLE)
    		    	->eq('date', $date)
    		    	->eq('column_id', $column_id)
    		    	->eq('user_id', $owner_id)
    				->findOne();
    }
    
    /**
     * Extract WIP information
     * 
     * @author Antonio Rabelo
     * @return array  
     */
    private function extractWIPInfo() {
    	/* 
    	 * TODO convert all UTC epoch (date column) to the current timezone date
    	 * TODO fill all gaps between unmodified columns and 
    	 * assigned users (repeat quantity information from the day before).
    	 */
    	return array();
    }
}
