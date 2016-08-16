<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProcedureAuthorization;
use Kanboard\Api\Authorization\UserAuthorization;
use Kanboard\Core\Base;
use ReflectionClass;

/**
 * Base class
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
abstract class BaseProcedure extends Base
{
    public function beforeProcedure($procedure)
    {
        ProcedureAuthorization::getInstance($this->container)->check($procedure);
        UserAuthorization::getInstance($this->container)->check($this->getClassName(), $procedure);
    }

    protected function formatTask($task)
    {
        if (! empty($task)) {
            $task['url'] = $this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), '', true);
            $task['color'] = $this->colorModel->getColorProperties($task['color_id']);
        }

        return $task;
    }

    protected function formatTasks($tasks)
    {
        if (! empty($tasks)) {
            foreach ($tasks as &$task) {
                $task = $this->formatTask($task);
            }
        }

        return $tasks;
    }

    protected function formatProject($project)
    {
        if (! empty($project)) {
            $project['url'] = array(
                'board' => $this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id']), '', true),
                'calendar' => $this->helper->url->to('CalendarController', 'show', array('project_id' => $project['id']), '', true),
                'list' => $this->helper->url->to('TaskListController', 'show', array('project_id' => $project['id']), '', true),
            );
        }

        return $project;
    }

    protected function formatProjects($projects)
    {
        if (! empty($projects)) {
            foreach ($projects as &$project) {
                $project = $this->formatProject($project);
            }
        }

        return $projects;
    }

    protected function filterValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        return $values;
    }

    protected function getClassName()
    {
        $reflection = new ReflectionClass(get_called_class());
        return $reflection->getShortName();
    }
}
