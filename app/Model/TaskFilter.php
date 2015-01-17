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
    private $query;

    public function create()
    {
        $this->query = $this->db->table(Task::TABLE);
        return $this;
    }

    public function filterByProject($project_id)
    {
        if ($project_id > 0) {
            $this->query->eq('project_id', $project_id);
        }

        return $this;
    }

    public function filterByCategory($category_id)
    {
        if ($category_id >= 0) {
            $this->query->eq('category_id', $category_id);
        }

        return $this;
    }

    public function filterByOwner($owner_id)
    {
        if ($owner_id >= 0) {
            $this->query->eq('owner_id', $owner_id);
        }

        return $this;
    }

    public function filterByColor($color_id)
    {
        if ($color_id !== '') {
            $this->query->eq('color_id', $color_id);
        }

        return $this;
    }

    public function filterByColumn($column_id)
    {
        if ($column_id >= 0) {
            $this->query->eq('column_id', $column_id);
        }

        return $this;
    }

    public function filterBySwimlane($swimlane_id)
    {
        if ($swimlane_id >= 0) {
            $this->query->eq('swimlane_id', $swimlane_id);
        }

        return $this;
    }

    public function filterByStatus($is_active)
    {
        if ($is_active >= 0) {
            $this->query->eq('is_active', $is_active);
        }

        return $this;
    }

    public function filterByDueDateRange($start, $end)
    {
        $this->query->gte('date_due', $this->dateParser->getTimestampFromIsoFormat($start));
        $this->query->lte('date_due', $this->dateParser->getTimestampFromIsoFormat($end));

        return $this;
    }

    public function findAll()
    {
        return $this->query->findAll();
    }

    public function toCalendarEvents()
    {
        $events = array();

        foreach ($this->query->findAll() as $task) {
            $events[] = array(
                'id' => $task['id'],
                'title' => t('#%d', $task['id']).' '.$task['title'],
                'start' => date('Y-m-d', $task['date_due']),
                'end' => date('Y-m-d', $task['date_due']),
                'allday' => true,
                'backgroundColor' => $this->color->getBackgroundColor($task['color_id']),
                'borderColor' => $this->color->getBorderColor($task['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
            );
        }

        return $events;
    }
}
