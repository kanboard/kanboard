<?php

namespace Model;

/**
 * Task Filter
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskFilter extends Base
{
    /**
     * Query
     *
     * @access public
     * @var \PicoDb\Table
     */
    public $query;

    /**
     * Create a new query
     *
     * @access public
     * @return TaskFilter
     */
    public function create()
    {
        $this->query = $this->db->table(Task::TABLE);
        return $this;
    }

    /**
     * Clone the filter
     *
     * @access public
     * @return TaskFilter
     */
    public function copy()
    {
        $filter = clone($this);
        $filter->query = clone($this->query);
        return $filter;
    }

    /**
     * Exclude a list of task_id
     *
     * @access public
     * @param  array  $task_ids
     * @return TaskFilter
     */
    public function excludeTasks(array $task_ids)
    {
        $this->query->notin('id', $task_ids);
        return $this;
    }

    /**
     * Filter by id
     *
     * @access public
     * @param  integer  $task_id
     * @return TaskFilter
     */
    public function filterById($task_id)
    {
        if ($task_id > 0) {
            $this->query->eq('id', $task_id);
        }

        return $this;
    }

    /**
     * Filter by title
     *
     * @access public
     * @param  string  $title
     * @return TaskFilter
     */
    public function filterByTitle($title)
    {
        $this->query->ilike('title', '%'.$title.'%');
        return $this;
    }

    /**
     * Filter by a list of project id
     *
     * @access public
     * @param  array  $project_ids
     * @return TaskFilter
     */
    public function filterByProjects(array $project_ids)
    {
        $this->query->in('project_id', $project_ids);
        return $this;
    }

    /**
     * Filter by project id
     *
     * @access public
     * @param  integer  $project_id
     * @return TaskFilter
     */
    public function filterByProject($project_id)
    {
        if ($project_id > 0) {
            $this->query->eq('project_id', $project_id);
        }

        return $this;
    }

    /**
     * Filter by category id
     *
     * @access public
     * @param  integer  $category_id
     * @return TaskFilter
     */
    public function filterByCategory($category_id)
    {
        if ($category_id >= 0) {
            $this->query->eq('category_id', $category_id);
        }

        return $this;
    }

    /**
     * Filter by assignee
     *
     * @access public
     * @param  integer  $owner_id
     * @return TaskFilter
     */
    public function filterByOwner($owner_id)
    {
        if ($owner_id >= 0) {
            $this->query->eq('owner_id', $owner_id);
        }

        return $this;
    }

    /**
     * Filter by color
     *
     * @access public
     * @param  string  $color_id
     * @return TaskFilter
     */
    public function filterByColor($color_id)
    {
        if ($color_id !== '') {
            $this->query->eq('color_id', $color_id);
        }

        return $this;
    }

    /**
     * Filter by column
     *
     * @access public
     * @param  integer $column_id
     * @return TaskFilter
     */
    public function filterByColumn($column_id)
    {
        if ($column_id >= 0) {
            $this->query->eq('column_id', $column_id);
        }

        return $this;
    }

    /**
     * Filter by swimlane
     *
     * @access public
     * @param  integer  $swimlane_id
     * @return TaskFilter
     */
    public function filterBySwimlane($swimlane_id)
    {
        if ($swimlane_id >= 0) {
            $this->query->eq('swimlane_id', $swimlane_id);
        }

        return $this;
    }

    /**
     * Filter by status
     *
     * @access public
     * @param  integer  $is_active
     * @return TaskFilter
     */
    public function filterByStatus($is_active)
    {
        if ($is_active >= 0) {
            $this->query->eq('is_active', $is_active);
        }

        return $this;
    }

    /**
     * Filter by due date (range)
     *
     * @access public
     * @param  integer  $start
     * @param  integer  $end
     * @return TaskFilter
     */
    public function filterByDueDateRange($start, $end)
    {
        $this->query->gte('date_due', $this->dateParser->getTimestampFromIsoFormat($start));
        $this->query->lte('date_due', $this->dateParser->getTimestampFromIsoFormat($end));

        return $this;
    }

    /**
     * Filter by start date (range)
     *
     * @access public
     * @param  integer $start
     * @param  integer $end
     * @return TaskFilter
     */
    public function filterByStartDateRange($start, $end)
    {
        $this->query->addCondition($this->getCalendarCondition(
            $this->dateParser->getTimestampFromIsoFormat($start),
            $this->dateParser->getTimestampFromIsoFormat($end),
            'date_started',
            'date_completed'
        ));

        return $this;
    }

    /**
     * Filter by creation date
     *
     * @access public
     * @param  integer  $start
     * @param  integer  $end
     * @return TaskFilter
     */
    public function filterByCreationDateRange($start, $end)
    {
        $this->query->addCondition($this->getCalendarCondition(
            $this->dateParser->getTimestampFromIsoFormat($start),
            $this->dateParser->getTimestampFromIsoFormat($end),
            'date_creation',
            'date_completed'
        ));

        return $this;
    }

    /**
     * Get all results of the filter
     *
     * @access public
     * @return array
     */
    public function findAll()
    {
        return $this->query->findAll();
    }

    /**
     * Format the results to the ajax autocompletion
     *
     * @access public
     * @return array
     */
    public function toAutoCompletion()
    {
        return $this->query->columns('id', 'title')->filter(function(array $results) {

            foreach ($results as &$result) {
                $result['value'] = $result['title'];
                $result['label'] = '#'.$result['id'].' - '.$result['title'];
            }

            return $results;

        })->findAll();
    }

    /**
     * Transform results to calendar events
     *
     * @access public
     * @param  string  $start_column    Column name for the start date
     * @param  string  $end_column      Column name for the end date
     * @return array
     */
    public function toDateTimeCalendarEvents($start_column, $end_column)
    {
        $events = array();

        foreach ($this->query->findAll() as $task) {

            $events[] = array_merge(
                $this->getTaskCalendarProperties($task),
                array(
                    'start' => date('Y-m-d\TH:i:s', $task[$start_column]),
                    'end' => date('Y-m-d\TH:i:s', $task[$end_column] ?: time()),
                    'editable' => false,
                )
            );
        }

        return $events;
    }

    /**
     * Transform results to all day calendar events
     *
     * @access public
     * @param  string    $column   Column name for the date
     * @return array
     */
    public function toAllDayCalendarEvents($column = 'date_due')
    {
        $events = array();

        foreach ($this->query->findAll() as $task) {

            $events[] = array_merge(
                $this->getTaskCalendarProperties($task),
                array(
                    'start' => date('Y-m-d', $task[$column]),
                    'end' => date('Y-m-d', $task[$column]),
                    'allday' => true,
                )
            );
        }

        return $events;
    }
}
